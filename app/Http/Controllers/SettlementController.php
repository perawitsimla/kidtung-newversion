<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SettlementController extends Controller
{
    // ==========================================
    // 1. หน้าสรุปยอดเคลียร์หนี้ทั้งหมด (Group Summary)
    // ==========================================
    public function index($groupId)
    {
        $group = Group::with('members')->findOrFail($groupId);
        
        // 1. ดึงหนี้ดิบจาก Algorithm (ที่ยังไม่หักลบการโอน)
        $rawDebts = $this->calculateNetDebts($groupId);
        
        // 2. ดึงประวัติการโอนเงินทั้งหมดในกลุ่ม
        $settlements = Settlement::where('group_id', $groupId)->orderBy('created_at', 'desc')->get();

        $summaryRows = [];

        // 3. เอาหนี้ดิบมาเช็คกับประวัติการโอนเงิน
        foreach ($rawDebts as $debt) {
            $debtorId = $debt['debtor_id'];
            $creditorId = $debt['creditor_id'];
            $debtAmount = $debt['amount'];

            $debtor = $group->members->firstWhere('id', $debtorId);
            $creditor = $group->members->firstWhere('id', $creditorId);

            // หารายการโอนเงินล่าสุดของ "ลูกหนี้ -> เจ้าหนี้" คู่นี้
            $latestSettlement = $settlements->where('debtor_id', $debtorId)->where('creditor_id', $creditorId)->first();

            // คำนวณยอดที่ "ชำระแล้ว (approved)" เพื่อเอาไปหักลบหนี้
            $approvedAmount = $settlements->where('debtor_id', $debtorId)
                                          ->where('creditor_id', $creditorId)
                                          ->where('status', 'approved')
                                          ->sum('amount');

            // ยอดคงเหลือที่ต้องจ่าย
            $remainingDebt = $debtAmount - $approvedAmount;

            // --- จัดกลุ่มสถานะเพื่อส่งไปโชว์ในตาราง ---
            if ($remainingDebt <= 0) {
                // กรณีจ่ายครบแล้ว
                $summaryRows[] = [
                    'debtor' => $debtor,
                    'creditor' => $creditor,
                    'amount' => $debtAmount, // โชว์ยอดเต็ม
                    'status' => 'ชำระแล้ว',
                    'status_color' => 'green',
                    'action' => 'detail',
                    'settlement_id' => $latestSettlement->id ?? null,
                ];
            } else {
                // กรณีค้างชำระ (ดูสถานะบิลล่างสุด)
                if ($latestSettlement && $latestSettlement->status === 'pending') {
                    $summaryRows[] = [
                        'debtor' => $debtor,
                        'creditor' => $creditor,
                        'amount' => $remainingDebt,
                        'status' => 'รอยืนยัน',
                        'status_color' => '#d48806', // สีส้ม
                        'action' => 'detail',
                        'settlement_id' => $latestSettlement->id,
                    ];
                } elseif ($latestSettlement && $latestSettlement->status === 'rejected') {
                    $summaryRows[] = [
                        'debtor' => $debtor,
                        'creditor' => $creditor,
                        'amount' => $remainingDebt,
                        'status' => 'ปฏิเสธ',
                        'status_color' => 'red',
                        'action' => 'pay',
                        'settlement_id' => $latestSettlement->id,
                    ];
                } else {
                    $summaryRows[] = [
                        'debtor' => $debtor,
                        'creditor' => $creditor,
                        'amount' => $remainingDebt,
                        'status' => 'ค้างชำระ',
                        'status_color' => 'black',
                        'action' => 'pay',
                        'settlement_id' => null,
                    ];
                }
            }
        }

        return view('groups.summary', compact('group', 'summaryRows', 'settlements'));
    }


    // ==========================================
    // คำนวณหนี้สุทธิแบบรายคู่ (Pairwise Netting Algorithm)
    // ==========================================
    public function calculateNetDebts($groupId)
    {
        /* 
           โครงสร้างที่เราต้องการสร้างคือ Array เก็บว่า A ค้าง B เท่าไหร่
           [
               'User_1' => [ 'User_2' => 150, 'User_3' => 50 ], // 1 ค้าง 2(150บ.) และ 1 ค้าง 3(50บ.)
               'User_2' => [ 'User_3' => 20 ]
           ]
        */
        $balances = [];

        // 1. ดึงข้อมูลบิลทั้งหมดในกลุ่ม (ดึงมาให้ครบถึงระดับคนหาร)
        $expenses = Expense::with(['items.splits'])->where('group_id', $groupId)->get();

        // 2. วนลูปคิดเงินทีละบิล
        foreach ($expenses as $expense) {
            
            // ยอดภาษีของบิลนี้
            $scPercent = $expense->sc_percent / 100;
            $vatPercent = $expense->vat_percent / 100;

            // วนลูปทีละเมนูอาหารในบิล
            foreach ($expense->items as $item) {
                $payerId = $item->payer_id; // คนออกเงิน (เจ้าหนี้)
                $itemPrice = $item->price;

                // วนลูปคนที่ต้องหารค่าเมนูนี้ (ลูกหนี้)
                foreach ($item->splits as $split) {
                    $debtorId = $split->user_id;

                    // ถ้าคนออกเงิน กับคนกิน คือคนเดียวกัน (ออกให้ตัวเอง) ไม่ต้องคิดหนี้
                    if ($payerId == $debtorId) {
                        continue;
                    }

                    // คำนวณราคาที่ต้องหาร (ราคาดิบ + SC + VAT)
                    $rawAmount = $split->amount; // ราคาดิบที่หารแล้ว
                    $scAmount = $rawAmount * $scPercent;
                    $amountAfterSc = $rawAmount + $scAmount;
                    $vatAmount = $amountAfterSc * $vatPercent;
                    
                    // ยอดหนี้สุทธิที่ต้องจ่ายสำหรับเมนูนี้
                    $finalOwedAmount = $amountAfterSc + $vatAmount;

                    // บันทึกหนี้ลงสมุดบัญชี (เพิ่มหนี้ให้ debtorId ที่ค้าง payerId)
                    if (!isset($balances[$debtorId])) {
                        $balances[$debtorId] = [];
                    }
                    if (!isset($balances[$debtorId][$payerId])) {
                        $balances[$debtorId][$payerId] = 0;
                    }
                    
                    $balances[$debtorId][$payerId] += $finalOwedAmount;
                }
            }
        }

        // 3. ขั้นตอนการ "หักลบกลบหนี้ (Netting)"
        // ถ้า A ค้าง B 100 บาท และ B ก็ค้าง A 40 บาท -> สรุปคือ A ค้าง B แค่ 60 บาทพอ
        $netDebts = [];
        
        foreach ($balances as $debtorId => $creditors) {
            foreach ($creditors as $creditorId => $amountOwed) {
                
                // เช็คว่าเจ้าหนี้คนนี้ (creditor) เคยเป็นหนี้เรา (debtor) กลับบ้างไหม?
                $amountOwedBack = $balances[$creditorId][$debtorId] ?? 0;

                // หักลบกัน
                $netAmount = $amountOwed - $amountOwedBack;

                // ถ้าผลลัพธ์เป็นบวกแปลว่าเรา (debtor) ค้างเขามากกว่าที่เขาค้างเรา -> บันทึกเป็นลูกหนี้
                // ถ้าติดลบ หรือเป็น 0 ปล่อยผ่านไป (เดี๋ยวมันจะไปโผล่ในลูปที่อีกฝ่ายเป็น debtor เอง)
                if ($netAmount > 0) {
                    
                    // ปัดเศษให้เหลือ 2 ตำแหน่ง
                    $netAmount = round($netAmount, 2);

                    // สร้างก้อน Array ไว้ส่งไปหน้าเว็บ
                    $netDebts[] = [
                        'debtor_id' => $debtorId,
                        'creditor_id' => $creditorId,
                        'amount' => $netAmount
                    ];
                }
            }
        }

        return $netDebts;
    }
    // ==========================================
    // 2. หน้าเปิดฟอร์มชำระเงิน (สำหรับลูกหนี้)
    // ==========================================
    public function pay($groupId, $creditorId)
    {
        $group = Group::findOrFail($groupId);
        $creditor = User::findOrFail($creditorId);
        $debtorId = Auth::id();

        // ดึงยอดหนี้ดิบทั้งหมดมาหาว่า เราค้างคนนี้กี่บาท
        $netDebts = $this->calculateNetDebts($groupId);
        $debtAmount = 0;
        foreach ($netDebts as $debt) {
            if ($debt['debtor_id'] == $debtorId && $debt['creditor_id'] == $creditorId) {
                $debtAmount = $debt['amount'];
                break;
            }
        }

        // หักลบยอดที่เคยโอนไปแล้ว (approved) เผื่อมีการทยอยจ่าย
        $approvedAmount = Settlement::where('group_id', $groupId)
                                    ->where('debtor_id', $debtorId)
                                    ->where('creditor_id', $creditorId)
                                    ->where('status', 'approved')
                                    ->sum('amount');
                                    
        $remainingDebt = $debtAmount - $approvedAmount;

        // ถ้าจ่ายครบแล้ว ให้ดีดกลับไปหน้า Summary
        if ($remainingDebt <= 0) {
            return redirect()->route('groups.summary', $groupId)->with('error', 'คุณไม่มีหนี้ค้างชำระกับบุคคลนี้แล้ว');
        }

        return view('groups.pay', compact('group', 'creditor', 'remainingDebt'));
    }

    // ==========================================
    // 3. บันทึกสลิปการโอนเงิน (Submit ฟอร์ม)
    // ==========================================
    public function storePayment(Request $request, $groupId, $creditorId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transfer_datetime' => 'required|date', // ตรวจสอบวันที่
            'slip_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // บังคับอัปโหลดรูป ขนาดไม่เกิน 2MB
        ]);

        // อัปโหลดไฟล์ไปที่โฟลเดอร์ public/storage/slips
        $path = $request->file('slip_image')->store('slips', 'public');

        // บันทึกลงตาราง settlements สถานะเริ่มต้นคือ pending
        Settlement::create([
            'group_id' => $groupId,
            'debtor_id' => Auth::id(),
            'creditor_id' => $creditorId,
            'amount' => $request->amount,
            'transfer_datetime' => $request->transfer_datetime, // บันทึกวันที่
            'slip_image' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('groups.summary', $groupId)->with('success', 'ส่งสลิปเรียบร้อย รอยืนยันจากเจ้าหนี้');
    }
    // ==========================================
    // 4. หน้าดูรายละเอียดสลิป (Detail)
    // ==========================================
    public function show($id)
    {
        // ดึงข้อมูลการโอนเงิน พร้อมกับข้อมูลกลุ่ม ลูกหนี้ และเจ้าหนี้
        $settlement = Settlement::with(['group', 'debtor', 'creditor'])->findOrFail($id);
        
        return view('groups.show_settlement', compact('settlement'));
    }

    // ==========================================
    // 5. เจ้าหนี้ยืนยันรับเงิน (Approve)
    // ==========================================
    public function approve($id)
    {
        $settlement = Settlement::findOrFail($id);
        
        // เช็คสิทธิ์: ป้องกันคนอื่นที่ไม่ใช่เจ้าหนี้มากดยืนยัน
        if (Auth::id() !== $settlement->creditor_id) {
            abort(403, 'คุณไม่มีสิทธิ์ยืนยันรายการนี้');
        }

        // เปลี่ยนสถานะเป็น approved
        $settlement->update(['status' => 'approved']);

        return redirect()->route('groups.summary', $settlement->group_id)->with('success', 'ยืนยันการรับเงินเรียบร้อยแล้ว');
    }

    // ==========================================
    // 6. เจ้าหนี้ปฏิเสธสลิป (Reject)
    // ==========================================
    public function reject($id)
    {
        $settlement = Settlement::findOrFail($id);
        
        // เช็คสิทธิ์: ป้องกันคนอื่นที่ไม่ใช่เจ้าหนี้มากดปฏิเสธ
        if (Auth::id() !== $settlement->creditor_id) {
            abort(403, 'คุณไม่มีสิทธิ์ปฏิเสธรายการนี้');
        }

        // เปลี่ยนสถานะเป็น rejected
        $settlement->update(['status' => 'rejected']);

        return redirect()->route('groups.summary', $settlement->group_id)->with('error', 'ปฏิเสธการรับเงินแล้ว ลูกหนี้ต้องอัปโหลดสลิปใหม่');
    }
    
}