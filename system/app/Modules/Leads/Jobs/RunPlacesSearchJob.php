<?php

namespace App\Modules\Leads\Jobs;

use App\Modules\Leads\Models\SearchRun;
use App\Modules\Leads\Services\SearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunPlacesSearchJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $searchRunId
    ) {}

    public function backoff(): array
    {
        return [5, 15, 60];
    }

    public function handle(SearchService $service): void
    {
        $searchRun = SearchRun::query()->find($this->searchRunId);

        if (! $searchRun) {
            return;
        }

        $service->execute($searchRun);
    }
}
