<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait providing common query scopes for models.
 */
trait CommonQueryScopes
{
    /**
     * Scope a query to filter results by status.
     *
     * @param Builder $query
     * @param string|null $status
     * @return Builder
     */
    public function scopeFilterByStatus(Builder $query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope a query to search results by title.
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearchByTitle(Builder $query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        }
        return $query;
    }
}
