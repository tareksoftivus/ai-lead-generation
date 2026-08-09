<?php

namespace App\Modules\Leads\Services;

use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Services\GooglePlaces\GooglePlacesClient;
use App\Modules\Leads\Services\GooglePlaces\PlacesResultMapper;

class LeadEnrichmentService
{
    public function __construct(
        protected GooglePlacesClient $client,
        protected EmailFromWebsiteLookup $emailLookup
    ) {}

    /**
     * Fill in phone/website via Place Details (cached once on the Place, so
     * every future user who finds the same business benefits), then attempt
     * to find a contact email from the business's website.
     *
     * @return array{email: ?string}
     */
    public function enrich(Place $place): array
    {
        if (! $place->details_fetched_at) {
            $details = $this->client->placeDetails($place->google_place_id);
            $place->update(PlacesResultMapper::mapDetailsResult($details));
        }

        $email = $this->emailLookup->find($place->website);

        return ['email' => $email];
    }
}
