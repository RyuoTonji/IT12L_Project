<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();

            // Admin sees everything
            if ($user->role === 'admin') {
                return;
            }

            if ($user->branch_id) {
                $builder->where('branch_id', $user->branch_id);
            } else {
                // Users without branch (test/original accounts) see branchless records
                $builder->whereNull('branch_id');
            }
        }
    }
}
