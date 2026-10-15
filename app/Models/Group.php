<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'deleted_at')
            ->whereNull('group_members.deleted_at')
            ->withTimestamps();
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
