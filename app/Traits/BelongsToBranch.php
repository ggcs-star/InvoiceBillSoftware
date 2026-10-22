<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Shared tenant behaviour for every model that belongs to a branch:
 * auto-fills branch_id from the logged-in staff member on create, and
 * transparently scopes every query to that branch (admins see all).
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function ($model) {
            if (! $model->branch_id && $user = Auth::user()) {
                $model->branch_id = $user->branch_id;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
