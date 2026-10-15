<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // แสดงภาพรวมการใช้จ่ายและรายการที่ต้องจัดการ
    public function index(SettlementController $settlementController): View
    {
        $user = Auth::user();

        $myGroups = $user->groups()
            ->withCount('members')
            ->latest()
            ->get();

        $pendingApprovals = Settlement::with(['debtor', 'group'])
            ->where('creditor_id', $user->id)
            ->where('status', 'pending')
            ->latest('transfer_datetime')
            ->take(5)
            ->get();

        $rejectedPayments = Settlement::with(['creditor', 'group'])
            ->where('debtor_id', $user->id)
            ->where('status', 'rejected')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recentSettlements = Settlement::with(['group', 'debtor', 'creditor'])
            ->where(function ($query) use ($user) {
                $query->where('debtor_id', $user->id)
                    ->orWhere('creditor_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        $debts = $this->getUserDebts($myGroups, $user->id, $settlementController);
        $totalPayable = collect($debts['payables'])->sum('amount');
        $totalReceivable = collect($debts['receivables'])->sum('amount');

        $expenses = Expense::with('items')
            ->whereIn('group_id', $myGroups->pluck('id'))
            ->get();

        $totalGroupSpending = 0;
        foreach ($expenses as $expense) {
            $subtotal = $expense->items->sum('price');
            $afterServiceCharge = $subtotal + ($subtotal * $expense->sc_percent / 100);
            $totalGroupSpending += $afterServiceCharge + ($afterServiceCharge * $expense->vat_percent / 100);
        }

        return view('dashboard', [
            'myGroups' => $myGroups,
            'pendingApprovals' => $pendingApprovals,
            'rejectedPayments' => $rejectedPayments,
            'recentSettlements' => $recentSettlements,
            'totalPayable' => round($totalPayable, 2),
            'totalReceivable' => round($totalReceivable, 2),
            'totalGroupSpending' => round($totalGroupSpending, 2),
            'payables' => $debts['payables'],
            'receivables' => $debts['receivables'],
        ]);
    }

    // แสดงประวัติการโอนเงินของผู้ใช้
    public function history(): View
    {
        $userId = Auth::id();

        $histories = Settlement::with(['group', 'debtor', 'creditor'])
            ->where(function ($query) use ($userId) {
                $query->where('debtor_id', $userId)
                    ->orWhere('creditor_id', $userId);
            })
            ->latest('transfer_datetime')
            ->get();

        return view('history', compact('histories'));
    }

    // สรุปยอดหนี้ที่ต้องจ่ายและยอดที่ต้องได้รับจากทุกกลุ่ม
    public function settlements(SettlementController $settlementController): View
    {
        $user = Auth::user();
        $myGroups = $user->groups()->get();
        $debts = $this->getUserDebts($myGroups, $user->id, $settlementController);

        return view('settlements_global', [
            'myPayables' => $debts['payables'],
            'myReceivables' => $debts['receivables'],
        ]);
    }

    // คำนวณยอดคงเหลือของผู้ใช้ในแต่ละคู่ หลังหักรายการที่ยืนยันรับเงินแล้ว
    private function getUserDebts($groups, int $userId, SettlementController $settlementController): array
    {
        $myPayables = [];
        $myReceivables = [];

        foreach ($groups as $group) {
            $allDebts = $settlementController->calculateNetDebts($group->id);
            $approvedSettlements = Settlement::where('group_id', $group->id)
                ->where('status', 'approved')
                ->get();

            foreach ($allDebts as $debt) {
                if ((int) $debt['debtor_id'] === $userId) {
                    $paidAmount = $approvedSettlements
                        ->where('debtor_id', $userId)
                        ->where('creditor_id', $debt['creditor_id'])
                        ->sum('amount');
                    $remaining = round($debt['amount'] - $paidAmount, 2);

                    if ($remaining > 0) {
                        $person = User::find($debt['creditor_id']);
                        if ($person) {
                            $myPayables[] = [
                                'group' => $group,
                                'person' => $person,
                                'amount' => $remaining,
                            ];
                        }
                    }
                }

                if ((int) $debt['creditor_id'] === $userId) {
                    $paidAmount = $approvedSettlements
                        ->where('debtor_id', $debt['debtor_id'])
                        ->where('creditor_id', $userId)
                        ->sum('amount');
                    $remaining = round($debt['amount'] - $paidAmount, 2);

                    if ($remaining > 0) {
                        $person = User::find($debt['debtor_id']);
                        if ($person) {
                            $myReceivables[] = [
                                'group' => $group,
                                'person' => $person,
                                'amount' => $remaining,
                            ];
                        }
                    }
                }
            }
        }

        return [
            'payables' => $myPayables,
            'receivables' => $myReceivables,
        ];
    }
}
