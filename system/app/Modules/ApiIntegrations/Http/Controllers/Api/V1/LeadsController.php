<?php

namespace App\Modules\ApiIntegrations\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\ApiIntegrations\Services\ApiKeyService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class LeadsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeReadToken($request);

        $validated = $request->validate([
            'list_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', Rule::in(array_keys(Lead::statuses()))],
            'min_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'integer', 'min:1'],
        ]);

        $limit = (int) ($validated['limit'] ?? 25);
        $userId = $request->user()->id;

        $query = Lead::query()
            ->forUser($userId)
            ->with(['place', 'tags', 'lists'])
            ->orderByDesc('id');

        if (! empty($validated['list_id'])) {
            $list = LeadList::query()->forUser($userId)->findOrFail($validated['list_id']);
            $query->whereHas('lists', fn ($listQuery) => $listQuery->whereKey($list->id));
        }

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['min_score'])) {
            $query->where('score', '>=', $validated['min_score']);
        }

        if (! empty($validated['cursor'])) {
            $query->where('id', '<', $validated['cursor']);
        }

        $leads = $query->limit($limit + 1)->get();
        $hasMore = $leads->count() > $limit;
        $page = $leads->take($limit)->values();

        return response()->json([
            'data' => $page->map(fn (Lead $lead) => $this->serializeLead($lead))->all(),
            'next_cursor' => $hasMore ? $page->last()?->id : null,
            'has_more' => $hasMore,
        ]);
    }

    public function show(Request $request, Lead $lead): JsonResponse
    {
        $this->authorizeReadToken($request);
        abort_unless((int) $lead->user_id === (int) $request->user()->id, 404);

        $lead->load(['place', 'tags', 'lists']);

        return response()->json([
            'data' => $this->serializeLead($lead),
        ]);
    }

    protected function serializeLead(Lead $lead): array
    {
        $place = $lead->place;

        return [
            'id' => $lead->id,
            'name' => $place?->name,
            'category' => $place?->google_category,
            'address' => $place?->formatted_address,
            'phone' => $place?->phone,
            'email' => $lead->email,
            'website' => $place?->website,
            'score' => $lead->score,
            'score_signals' => $lead->score_signals ?? [],
            'status' => $lead->status,
            'tags' => $lead->tags->pluck('name')->values(),
            'lists' => $lead->lists->map(fn ($list) => ['id' => $list->id, 'name' => $list->name])->values(),
            'created_at' => $lead->created_at?->toJSON(),
            'updated_at' => $lead->updated_at?->toJSON(),
        ];
    }

    protected function authorizeReadToken(Request $request): void
    {
        $accessToken = $request->user()?->currentAccessToken();

        abort_unless(
            $accessToken instanceof PersonalAccessToken
            && $accessToken->can(ApiKeyService::ABILITY_READ),
            403
        );
    }
}
