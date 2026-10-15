<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // แสดงข้อมูลส่วนตัวและสถิติการใช้งานบัญชี
    public function edit(): View
    {
        $user = Auth::user();

        $groupsCount = $user->groups()->count();
        $paymentsMade = Settlement::where('debtor_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
        $paymentsReceived = Settlement::where('creditor_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
        $pendingCount = Settlement::where('creditor_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('profile.edit', compact(
            'user',
            'groupsCount',
            'paymentsMade',
            'paymentsReceived',
            'pendingCount'
        ));
    }

    // บันทึกข้อมูลส่วนตัว
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'promptpay_no' => ['nullable', 'string', 'max:20'],
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $validated['name'],
            'promptpay_no' => $validated['promptpay_no'] ?? null,
        ]);

        return redirect()->route('profile.edit')->with('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }
}
