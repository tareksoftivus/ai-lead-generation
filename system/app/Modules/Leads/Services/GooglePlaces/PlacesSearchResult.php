<?php

namespace App\Modules\Leads\Services\GooglePlaces;

class PlacesSearchResult
{
    /**
     * @param  array<int, array<string, mixed>>  $places  Raw place objects from the API response.
     */
    public function __construct(
        public readonly array $places,
        public readonly ?string $nextPageToken = null,
    ) {}

    public function hasMorePages(): bool
    {
        return $this->nextPageToken !== null;
    }
}
