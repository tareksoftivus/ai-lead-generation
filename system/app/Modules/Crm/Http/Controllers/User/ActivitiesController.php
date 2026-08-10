<?php

namespace App\Modules\Crm\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Services\ActivityFeedService;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivitiesController extends Controller
{
    public function __construct(protected ActivityFeedService $feed) {}

    public function index(Request $request): View
    {
        $leadIds = Lead::query()
            ->forUser($request->user()->id)
            ->pluck('id');

        $activities = LeadActivity::query()
            ->whereIn('lead_id', $leadIds)
            ->with(['lead.place', 'causedBy'])
            ->latest()
            ->limit(100)
            ->get();

        return view('crm::user.activities.index', [
            'activityGroups' => $this->feed->grouped($activities),
            'activities' => $activities,
            'leads' => Lead::query()->forUser($request->user()->id)->with('place')->orderByDesc('created_at')->get(),
            'leadCount' => $leadIds->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['note', 'call'])],
            'lead_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $lead = Lead::query()
            ->forUser($request->user()->id)
            ->findOrFail($validated['lead_id']);

        $lead->notes()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        LeadActivity::logNoteAdded($lead, $request->user(), $validated['type'], $validated['body']);

        return redirect()->route('user.activities.index')->with('success', __('Activity added.'));
    }
}
