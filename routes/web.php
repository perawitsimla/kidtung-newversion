<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // หน้าประวัติรวม (History)
    Route::get('/history', [DashboardController::class, 'history'])->name('history');
    // หน้าสรุปหนี้สินรวมทุกกลุ่ม (Global Settlements)
    Route::get('/settlements', [DashboardController::class, 'settlements'])->name('settlements.index');

    // หน้า My Groups
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    // ระบบรับรหัสเชิญเข้ากลุ่ม
    Route::post('/groups/join', [GroupController::class, 'join'])->name('groups.join');
    // หน้าฟอร์มสร้างกลุ่ม
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    // รับค่าจากฟอร์มเพื่อบันทึก
    Route::post('/groups/store', [GroupController::class, 'store'])->name('groups.store');
    // แสดงกลุ่มนั้น ๆ
    Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
    
    // หน้าเพิ่มรายการบิล (ชี้ไปที่ ExpenseController)
    Route::get('/groups/{id}/expenses/create', [ExpenseController::class, 'createExpense'])->name('expenses.create');
    // รับค่าจากฟอร์มเพิ่มบิล เพื่อบันทึกลง Database
    Route::post('/groups/{id}/expenses', [ExpenseController::class, 'storeExpense'])->name('expenses.store');
    // เข้าหน้าสรุปหนี้
    Route::get('/groups/{group}/summary', [SettlementController::class, 'index'])->name('groups.summary');
    // Route สำหรับเปิดหน้าฟอร์มจ่ายเงิน
    Route::get('/groups/{group}/pay/{creditor}', [SettlementController::class, 'pay'])->name('settlements.pay');
    // Route สำหรับรับข้อมูลรูปสลิปและบันทึกลง Database
    Route::post('/groups/{group}/pay/{creditor}', [SettlementController::class, 'storePayment'])->name('settlements.store');

    // ลบบิล
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    // หน้าฟอร์มแก้ไขบิล
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    // รับค่าจากฟอร์มเพื่ออัปเดตข้อมูลลงฐานข้อมูล (ใช้ Method PUT ตามมาตรฐาน RESTful)
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    
    // Route สำหรับเปิดดูหน้ารายละเอียดสลิป
    Route::get('/settlements/{settlement}', [SettlementController::class, 'show'])->name('settlements.show');

    // Route สำหรับเจ้าหนี้ กดยืนยัน (Approve)
    Route::patch('/settlements/{settlement}/approve', [SettlementController::class, 'approve'])->name('settlements.approve');

    // Route สำหรับเจ้าหนี้ กดปฏิเสธ (Reject)
    Route::patch('/settlements/{settlement}/reject', [SettlementController::class, 'reject'])->name('settlements.reject');
});

require __DIR__.'/settings.php';