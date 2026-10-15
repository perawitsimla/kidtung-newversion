<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ต้อง use SoftDeletes ด้วยเพราะใน Migration มี

class Settlement extends Model
{
    use HasFactory, SoftDeletes;

    // อนุญาตให้ Mass Assignment ได้
    protected $fillable = [
        'group_id',
        'debtor_id',
        'creditor_id',
        'amount',
        'transfer_datetime',
        'slip_image',
        'status',
    ];

    // ความสัมพันธ์: ไปยังตาราง Group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // เชื่อมความสัมพันธ์ไปตาราง User (ลูกหนี้ - คนจ่ายเงิน)
    public function debtor()
    {
        return $this->belongsTo(User::class, 'debtor_id');
    }

    // เชื่อมความสัมพันธ์ ไปตาราง User (เจ้าหนี้ - คนรับเงิน)
    public function creditor()
    {
        return $this->belongsTo(User::class, 'creditor_id');
    }
}