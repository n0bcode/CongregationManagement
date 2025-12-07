<?php

namespace App\Services;

use Illuminate\Support\Collection;

class GlobalSearchService
{
    /**
     * Search across multiple entities.
     *
     * @param string $query
     * @return Collection
     */
    public function search(string $query): Collection
    {
        if (strlen($query) < 3) {
            return collect();
        }

        // Placeholder for actual search logic across models
        // e.g., Member::search($query), Expense::search($query)
        
        $results = collect();

        // Example structure
        // $results->push(['type' => 'member', 'title' => 'John Doe', 'url' => '/members/1']);

        return $results;
    }
}
