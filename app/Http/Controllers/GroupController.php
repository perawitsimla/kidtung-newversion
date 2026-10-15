<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Settlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GroupController extends Controller
{
    // แสดงหน้าฟอร์มสร้างกลุ่ม
    public function create(): View
    {
        return view('groups.create');
    }

    // รับข้อมูลจากฟอร์มมาบันทึกลงฐานข้อมูล
    public function store(Request $request): RedirectResponse
    {
        // 1. ตรวจสอบข้อมูล (Validation)
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        try {
            // เริ่ม Transaction ถ้ามี error โค้ดจะไม่ถูกบันทึกลง Database
            DB::beginTransaction();

            // 2. สร้างกลุ่มใหม่
            $group = new Group;
            $group->name = $request->name;
            
            // สุ่มตัวอักษรภาษาอังกฤษผสมตัวเลข 4 หลัก และทำให้เป็นตัวพิมพ์ใหญ่
            $group->invite_code = Str::upper(Str::random(4));
            $group->save();

            // 3. เอาผู้ที่กำลังล็อกอินอยู่ เข้าไปเป็นสมาชิกกลุ่มในตำแหน่ง Admin
            $member = new GroupMember;
            $member->group_id = $group->id;
            $member->user_id = (int) Auth::id(); // ดึง ID ของคนที่กำลังล็อกอิน
            $member->role = 'admin';
            $member->save();

            // ยืนยันการบันทึกข้อมูล
            DB::commit();

            // กลับไปหน้าไหนสักหน้า (เดี๋ยวเราค่อยมาสร้างหน้า index ทีหลัง)
            // ส่ง Flash Session ไปบอกด้วยว่าสร้างสำเร็จและรหัสอะไร
            return redirect()->back()->with('success', 'สร้างกลุ่มสำเร็จ! รหัสเชิญเพื่อนของคุณคือ: '.$group->invite_code);

        } catch (\Exception $e) {
            // ถ้าระหว่างเซฟข้อมูลมีปัญหา ให้ยกเลิกทั้งหมด
            DB::rollBack();

            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการสร้างกลุ่ม กรุณาลองใหม่');
        }
    }
    public function index(Request $request): View
    {
        // รับค่าคำค้นหา (ถ้ามี)
        $search = $request->input('search');

        // ดึงเฉพาะกลุ่มที่ User คนนี้เป็นสมาชิกอยู่ และกรองตามชื่อถ้ามีการค้นหา
        $user = Auth::user();

        $groups = $user->groups()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->withCount('members') // ให้นับจำนวนสมาชิกมาด้วยเลย
            ->latest()
            ->get();

        return view('groups.index', compact('groups', 'search'));
    }
    public function join(Request $request): RedirectResponse
    {
        $request->validate([
            'invite_code' => ['required', 'string', 'size:4']
        ]);

        // ค้นหากลุ่มจากรหัส (แปลงเป็นตัวพิมพ์ใหญ่เผื่อพิมพ์เล็กมา)
        $group = Group::where('invite_code', Str::upper($request->invite_code))->first();

        if (!$group) {
            return redirect()->back()->with('error', 'ไม่พบรหัสเชิญกลุ่มนี้ กรุณาตรวจสอบอีกครั้ง');
        }

        // เช็คว่า User คนนี้อยู่ในกลุ่มนี้อยู่แล้วหรือไม่
        if ($group->members()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('info', 'คุณเป็นสมาชิกในกลุ่มนี้อยู่แล้ว');
        }

        // บันทึกการเข้าร่วมกลุ่ม
        $member = new GroupMember();
        $member->group_id = $group->id;
        /** @phpstan-ignore-next-line */
        $member->user_id = (int) Auth::id();
        $member->role = 'member'; // คนเข้าทีหลังได้สิทธิ์เป็นแค่ member
        $member->save();

        return redirect()->route('groups.index')->with('success', 'เข้าร่วมกลุ่ม ' . $group->name . ' สำเร็จ!');
    }
    public function show($id, SettlementController $settlementController): View
    {
        // ค้นหากลุ่ม พร้อมดึงกลุ่ม + สมาชิก + ประวัติบิลเรียงจากล่าสุดไปเก่าสุด
        $group = Group::with(['members', 'expenses' => function($query) {
            $query->latest()->with('items.payer'); // ดึงรายการย่อยและคนจ่ายมาด้วย
        }])->findOrFail($id);

        // เช็คว่า User ที่ล็อกอินอยู่ เป็นสมาชิกของกลุ่มนี้หรือไม่ (ป้องกันคนเนียนพิมพ์ URL เข้ามาดู)
        if (!$group->members->contains(Auth::id())) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงกลุ่มนี้');
        }

        // ค้นหาข้อมูลว่า User คนนี้เป็น Admin หรือ Member ในกลุ่มนี้
        $myRole = $group->members->where('id', Auth::id())->first()->pivot->role;

        // --- คำนวณยอดหนี้ที่ "ฉันต้องจ่ายคนอื่น" เพื่อไปโชว์ใน Block 3 ---
        $userId = Auth::id();
        $myDebts = [];

        // 1. ยืมสมองกลคำนวณหนี้ (เพื่อหาหนี้ดิบทั้งหมด)
        // ใช้ตัวคำนวณหนี้ร่วมกับหน้าสรุปหนี้
        $allDebts = $settlementController->calculateNetDebts($id);

        // 2. ดึงประวัติการโอนเงิน (เฉพาะที่มีสถานะ approved)
        $approvedSettlements = Settlement::where('group_id', $id)
            ->where('status', 'approved')
            ->get();

        // 3. กรองเอาเฉพาะ "รายการที่ฉันเป็นลูกหนี้"
        foreach ($allDebts as $debt) {
            if ($debt['debtor_id'] == $userId) {
                
                // หายอดที่โอนไปแล้ว ของคู่กรณีนี้
                $paidAmount = $approvedSettlements
                    ->where('debtor_id', $userId)
                    ->where('creditor_id', $debt['creditor_id'])
                    ->sum('amount');

                // ยอดหนี้คงเหลือ
                $remainingAmount = $debt['amount'] - $paidAmount;

                if ($remainingAmount > 0) {
                    // ดึงชื่อเจ้าหนี้จาก Relationship ของ Group ที่โหลดมาแล้ว
                    $creditorName = $group->members->firstWhere('id', $debt['creditor_id'])->name;
                    
                    $myDebts[] = [
                        'creditor_name' => $creditorName,
                        'amount' => $remainingAmount
                    ];
                }
            }
        }

        // อย่าลืมส่งตัวแปร $myDebts ไปให้หน้าเว็บด้วยนะ
        return view('groups.show', compact('group', 'myRole', 'myDebts'));
    }
}