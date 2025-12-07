<?php

namespace Tests\Unit\Services;

use App\Services\GlobalSearchService;
use Tests\TestCase;

class GlobalSearchServiceTest extends TestCase
{
    public function test_it_returns_empty_collection_for_short_query()
    {
        $service = new GlobalSearchService();

        $results = $service->search('ab');

        $this->assertTrue($results->isEmpty());
    }

    public function test_it_returns_collection_for_valid_query()
    {
        $service = new GlobalSearchService();

        $results = $service->search('test query');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $results);
    }
}
