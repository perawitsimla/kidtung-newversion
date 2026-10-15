<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSplit extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'expense_item_id', 
        'user_id', 
        'amount', 
        'split_method'
    ];

    public function expenseItem(): BelongsTo
    {
        return $this->belongsTo(ExpenseItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
