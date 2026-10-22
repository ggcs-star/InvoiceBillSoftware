<?php

namespace App\Models\Scopes;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Automatically restricts every query on a branch-owned model to the
 * authenticated staff member's branch, unless the user is a platform Admin.
 */
class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user || $user->role === UserRole::Admin) {
            return;
        }

        $builder->where($model->qualifyColumn('branch_id'), $user->branch_id);
    }
}
