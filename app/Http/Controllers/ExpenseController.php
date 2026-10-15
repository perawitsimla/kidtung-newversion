<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Group;
use App\Models\ItemSplit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    // แสดงแบบฟอร์มเพิ่มบิล
    public function createExpense($id): View
    {
        $group = Group::with('members')->findOrFail($id);

        if (!$group->members->contains(Auth::id())) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงกลุ่มนี้');
        }

        return view('expenses.create', compact('group'));
    }

    // บันทึกบิลและรายการแบ่งเงินทั้งหมด
    public function storeExpense(Request $request, $id): RedirectResponse
    {
        $validated = $this->validateExpense($request);
        $group = Group::with('members')->findOrFail($id);
        if (!$group->members->contains(Auth::id())) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงกลุ่มนี้');
        }
        $this->checkExpenseItems($validated['items'], $group);

        DB::transaction(function () use ($validated, $request, $group) {
            $expense = Expense::create([
                'group_id' => $group->id,
                'title' => $validated['title'],
                'sc_percent' => $request->has('has_sc') ? (float) ($validated['sc_percent'] ?? 0) : 0,
                'vat_percent' => $request->has('has_vat') ? (float) ($validated['vat_percent'] ?? 0) : 0,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $expenseItem = ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'payer_id' => $itemData['payer_id'],
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                ]);

                $this->saveSplits($expenseItem, $itemData);
            }
        });

        return redirect()->route('groups.show', $group->id)
            ->with('success', 'บันทึกรายการค่าใช้จ่ายเรียบร้อยแล้ว');
    }

    // ลบบิล โดยอนุญาตให้ผู้สร้างบิลหรือแอดมินกลุ่มลบได้
    public function destroy($id): RedirectResponse
    {
        $expense = Expense::findOrFail($id);
        $myRole = DB::table('group_members')
            ->where('group_id', $expense->group_id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->value('role');

        if (!$myRole || ($myRole !== 'admin' && (int) $expense->created_by !== (int) Auth::id())) {
            abort(403, 'คุณไม่มีสิทธิ์ลบบิลนี้');
        }

        $groupId = $expense->group_id;
        $expense->delete();

        return redirect()->route('groups.show', $groupId)
            ->with('success', 'ลบรายการบิลเรียบร้อยแล้ว');
    }

    // แสดงแบบฟอร์มแก้ไขบิล
    public function edit($id): View
    {
        $expense = Expense::with(['items.splits'])->findOrFail($id);
        $this->checkExpensePermission($expense);

        $group = Group::with('members')->findOrFail($expense->group_id);

        return view('expenses.edit', compact('expense', 'group'));
    }

    // บันทึกการแก้ไขบิล
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $this->validateExpense($request);
        $expense = Expense::findOrFail($id);
        $this->checkExpensePermission($expense);

        $group = Group::with('members')->findOrFail($expense->group_id);
        $this->checkExpenseItems($validated['items'], $group, $expense);

        DB::transaction(function () use ($validated, $request, $expense, $group) {
            $expense->update([
                'title' => $validated['title'],
                'sc_percent' => $request->has('has_sc') ? (float) ($validated['sc_percent'] ?? 0) : 0,
                'vat_percent' => $request->has('has_vat') ? (float) ($validated['vat_percent'] ?? 0) : 0,
            ]);

            $submittedItemIds = [];
            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['id'])) {
                    $submittedItemIds[] = (int) $itemData['id'];
                }
            }

            $oldItems = ExpenseItem::where('expense_id', $expense->id)->get();
            foreach ($oldItems as $oldItem) {
                if (!in_array($oldItem->id, $submittedItemIds, true)) {
                    ItemSplit::where('expense_item_id', $oldItem->id)->delete();
                    $oldItem->delete();
                }
            }

            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['id'])) {
                    $expenseItem = ExpenseItem::where('expense_id', $expense->id)
                        ->findOrFail($itemData['id']);

                    $expenseItem->update([
                        'payer_id' => $itemData['payer_id'],
                        'name' => $itemData['name'],
                        'price' => $itemData['price'],
                    ]);

                    ItemSplit::where('expense_item_id', $expenseItem->id)->delete();
                } else {
                    $expenseItem = ExpenseItem::create([
                        'expense_id' => $expense->id,
                        'payer_id' => $itemData['payer_id'],
                        'name' => $itemData['name'],
                        'price' => $itemData['price'],
                    ]);
                }

                $this->saveSplits($expenseItem, $itemData);
            }
        });

        return redirect()->route('groups.show', $expense->group_id)
            ->with('success', 'แก้ไขรายการบิลเรียบร้อยแล้ว');
    }

    // ตรวจสอบข้อมูลบิลก่อนบันทึก
    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:150'],
            'items.*.price' => ['required', 'numeric', 'min:0.01'],
            'items.*.payer_id' => ['required', 'integer', 'exists:users,id'],
            'items.*.split_method' => ['nullable', 'in:equal,exact,percent'],
            'items.*.shares' => ['required', 'array', 'min:1'],
            'items.*.shares.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'items.*.share_amounts' => ['nullable', 'array'],
            'items.*.share_amounts.*' => ['nullable', 'numeric', 'min:0'],
            'items.*.share_percents' => ['nullable', 'array'],
            'items.*.share_percents.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sc_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }

    // เช็กว่าคนจ่ายและคนหารเป็นสมาชิกของกลุ่มจริง และตรวจยอดตามวิธีแบ่งเงิน
    private function checkExpenseItems(array $items, Group $group, ?Expense $expense = null): void
    {
        $memberIds = $group->members->pluck('id')->map(function ($id) {
            return (int) $id;
        })->toArray();

        foreach ($items as $index => $item) {
            if (!in_array((int) $item['payer_id'], $memberIds, true)) {
                throw ValidationException::withMessages([
                    "items.$index.payer_id" => 'ผู้จ่ายเงินต้องเป็นสมาชิกในกลุ่มนี้',
                ]);
            }

            if (!empty($item['id'])) {
                $exists = ExpenseItem::where('expense_id', $expense?->id)
                    ->whereKey($item['id'])
                    ->exists();

                if (!$exists) {
                    throw ValidationException::withMessages([
                        "items.$index.id" => 'ไม่พบรายการนี้ในบิลที่กำลังแก้ไข',
                    ]);
                }
            }

            $shares = array_map('intval', $item['shares']);
            foreach ($shares as $userId) {
                if (!in_array($userId, $memberIds, true)) {
                    throw ValidationException::withMessages([
                        "items.$index.shares" => 'เลือกได้เฉพาะสมาชิกในกลุ่มนี้',
                    ]);
                }
            }

            $method = $item['split_method'] ?? 'equal';
            $price = round((float) $item['price'], 2);

            if ($method === 'exact') {
                $amounts = $item['share_amounts'] ?? [];
                $totalCents = 0;

                foreach ($shares as $userId) {
                    if (!isset($amounts[$userId]) || !is_numeric($amounts[$userId])) {
                        throw ValidationException::withMessages([
                            "items.$index.share_amounts" => 'กรอกยอดเงินของสมาชิกที่เลือกให้ครบทุกคน',
                        ]);
                    }
                    $totalCents += (int) round(((float) $amounts[$userId]) * 100);
                }

                $priceCents = (int) round($price * 100);
                if ($totalCents !== $priceCents) {
                    throw ValidationException::withMessages([
                        "items.$index.share_amounts" => 'ยอดที่ระบุรวมกันต้องเท่ากับราคาเมนู',
                    ]);
                }
            }

            if ($method === 'percent') {
                $percentages = $item['share_percents'] ?? [];
                $total = 0;

                foreach ($shares as $userId) {
                    if (!isset($percentages[$userId]) || !is_numeric($percentages[$userId])) {
                        throw ValidationException::withMessages([
                            "items.$index.share_percents" => 'กรอกเปอร์เซ็นต์ของสมาชิกที่เลือกให้ครบทุกคน',
                        ]);
                    }
                    $total += (float) $percentages[$userId];
                }

                if ((int) round($total * 100) !== 10000) {
                    throw ValidationException::withMessages([
                        "items.$index.share_percents" => 'เปอร์เซ็นต์ของทุกคนต้องรวมกันเท่ากับ 100%',
                    ]);
                }
            }
        }
    }

    // สร้างยอดที่แต่ละคนต้องรับผิดชอบ โดยใช้ schema เดิมของ item_splits
    private function saveSplits(ExpenseItem $expenseItem, array $itemData): void
    {
        $method = $itemData['split_method'] ?? 'equal';
        $userIds = array_map('intval', $itemData['shares']);
        $priceCents = (int) round(((float) $itemData['price']) * 100);
        $amounts = [];

        if ($method === 'exact') {
            foreach ($userIds as $userId) {
                $amounts[$userId] = round((float) $itemData['share_amounts'][$userId], 2);
            }
        } elseif ($method === 'percent') {
            $amountCents = [];
            $fractionalCents = [];
            $assignedCents = 0;

            foreach ($userIds as $userId) {
                $exactCents = $priceCents * ((float) $itemData['share_percents'][$userId] / 100);
                $wholeCents = (int) floor($exactCents);
                $amountCents[$userId] = $wholeCents;
                $fractionalCents[$userId] = $exactCents - $wholeCents;
                $assignedCents += $wholeCents;
            }

            arsort($fractionalCents, SORT_NUMERIC);
            $remainingCents = $priceCents - $assignedCents;
            foreach ($fractionalCents as $userId => $fraction) {
                if ($remainingCents <= 0) {
                    break;
                }
                $amountCents[$userId] += 1;
                $remainingCents -= 1;
            }

            foreach ($userIds as $userId) {
                $amounts[$userId] = $amountCents[$userId] / 100;
            }
        } else {
            $baseCents = intdiv($priceCents, count($userIds));
            $remainingCents = $priceCents % count($userIds);

            foreach ($userIds as $index => $userId) {
                $amountCents = $baseCents + ($index < $remainingCents ? 1 : 0);
                $amounts[$userId] = $amountCents / 100;
            }
            $method = 'equal';
        }

        foreach ($userIds as $userId) {
            ItemSplit::create([
                'expense_item_id' => $expenseItem->id,
                'user_id' => $userId,
                'amount' => $amounts[$userId],
                'split_method' => $method,
            ]);
        }
    }

    // เช็กสิทธิ์แก้ไขและลบบิล
    private function checkExpensePermission(Expense $expense): void
    {
        $myRole = DB::table('group_members')
            ->where('group_id', $expense->group_id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->value('role');

        if (!$myRole || ($myRole !== 'admin' && (int) $expense->created_by !== (int) Auth::id())) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขบิลนี้');
        }
    }
}
