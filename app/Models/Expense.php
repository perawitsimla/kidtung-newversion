<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    // อนุญาตให้ 4 คอลัมน์นี้ถูกบันทึกผ่านคำสั่ง create() ได้
    protected $fillable = [
        'group_id', 
        'title', 
        'vat_percent', 
        'sc_percent',
        'created_by' // เพิ่มบรรทัดนี้
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }
}
