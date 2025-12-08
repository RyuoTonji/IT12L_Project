<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BranchFiltered
{
    /**
     * Apply branch filter to query based on authenticated user's branch
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeForUserBranch(Builder $query): Builder
    {
        $user = auth()->user();

        // If no user is authenticated, return query as is
        if (!$user) {
            return $query;
        }

        // Admin and manager without specific branch see all data
        if (in_array($user->role, ['admin', 'manager']) && !$user->branch_id) {
            return $query;
        }

        // Branch-specific users only see their branch data
        if ($user->branch_id) {
            return $query->where($this->getTable() . '.branch_id', $user->branch_id);
        }

        return $query;
    }

    /**
     * Check if the authenticated user can access this model instance
     *
     * @return bool
     */
    public function canAccess(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Admin and manager without specific branch can access all
        if (in_array($user->role, ['admin', 'manager']) && !$user->branch_id) {
            return true;
        }

        // Branch-specific users can only access their branch data
        return $this->branch_id === $user->branch_id;
    }
}
