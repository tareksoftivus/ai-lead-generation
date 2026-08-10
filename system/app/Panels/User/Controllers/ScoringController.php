<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Services\LeadScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScoringController extends Controller
{
    public function __construct(
        protected LeadScoringService $scoring
    ) {}

    /**
     * The "Lead scoring" screen — weight the signals that order your leads.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $leadCount = Lead::query()->forUser($user->id)->count();
        $lists = LeadList::query()
            ->forUser($user->id)
            ->withCount('leads')
            ->orderBy('name')
            ->get();

        $sampleLeads = Lead::query()
            ->forUser($user->id)
            ->with('place')
            ->orderByDesc('score')
            ->latest()
            ->limit(5)
            ->get();

        return view('panels.user.scoring.index', [
            'weights' => LeadScoringService::DEFAULT_WEIGHTS,
            'leadCount' => $leadCount,
            'lists' => $lists,
            'sampleLeads' => $sampleLeads,
            'scoring' => $this->scoring,
        ]);
    }

    public function apply(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'offer' => ['nullable', 'string', 'max:1000'],
            'w_reviews' => ['required', 'integer', 'min:0', 'max:100'],
            'w_booking' => ['required', 'integer', 'min:0', 'max:100'],
            'w_age' => ['required', 'integer', 'min:0', 'max:100'],
            'w_competition' => ['required', 'integer', 'min:0', 'max:100'],
            'scope' => ['required', 'string', Rule::in(['all', ...LeadList::query()->forUser($request->user()->id)->pluck('id')->map(fn ($id) => (string) $id)->all()])],
        ]);

        $weights = $this->scoring->normalizeWeights([
            'reviews' => $validated['w_reviews'],
            'booking' => $validated['w_booking'],
            'age' => $validated['w_age'],
            'competition' => $validated['w_competition'],
        ]);

        if (array_sum($weights) <= 0) {
            return back()
                ->withInput()
                ->with('error', __('Set at least one scoring weight above zero.'));
        }

        $query = Lead::query()
            ->forUser($request->user()->id)
            ->with('place');

        $scopeLabel = __('Every lead');

        if ($validated['scope'] !== 'all') {
            $list = LeadList::query()
                ->forUser($request->user()->id)
                ->findOrFail((int) $validated['scope']);

            $scopeLabel = $list->name;
            $query->whereHas('lists', fn ($listQuery) => $listQuery->whereKey($list->id));
        }

        $result = $this->scoring->rescore($query->get(), $weights);

        Log::info('User applied lead scoring weights.', [
            'user_id' => $request->user()->id,
            'scope' => $validated['scope'],
            'scope_label' => $scopeLabel,
            'lead_count' => $result['count'],
            'changed_count' => $result['changed'],
            'weights' => $weights,
        ]);

        return redirect()
            ->route('user.scoring.index')
            ->with('success', __('Re-scored :count leads in ":scope". :changed changed position.', [
                'count' => $result['count'],
                'scope' => $scopeLabel,
                'changed' => $result['changed'],
            ]));
    }
}
