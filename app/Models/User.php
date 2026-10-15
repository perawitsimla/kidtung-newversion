<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    // เพิ่มบรรทัดนี้ เพื่อบอกระบบ Auth ว่าอนุญาตให้ 3 คอลัมน์นี้บันทึกข้อมูลแบบกลุ่มได้
    protected $fillable = [
        'name',
        'email',
        'password',
        'promptpay_no',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'deleted_at')
            ->whereNull('group_members.deleted_at')
            ->withTimestamps();
    }

    public function paidItems(): HasMany
    {
        return $this->hasMany(ExpenseItem::class, 'payer_id');
    }

    public function splitItems(): HasMany
    {
        return $this->hasMany(ItemSplit::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Settlement::class, 'debtor_id');
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Settlement::class, 'creditor_id');
    }

    public function initials(): string
    {
        // ตัดเอาตัวอักษรตัวแรกของชื่อมาทำเป็นตัวพิมพ์ใหญ่
        return strtoupper(substr($this->name, 0, 1));
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // ตัวนี้สำคัญมาก บังคับให้เข้ารหัสผ่านอัตโนมัติ
        ];
    }
}
