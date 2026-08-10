<?php

namespace App\Modules\Crm\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PipelineController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->forUser($request->user()->id)
            ->where('is_in_pipeline', true)
            ->with(['place', 'tags'])
            ->latest('pipeline_entered_at')
            ->latest()
            ->get();

        $stages = collect(Lead::statuses())
            ->map(fn (array $status, string $key): array => [
                'key' => $key,
                'label' => $status['label'],
                'variant' => $status['variant'],
                'leads' => $leads->where('status', $key)->values(),
            ])
            ->all();

        return view('crm::user.pipeline.index', [
            'leads' => $leads,
            'stages' => $stages,
            'tags' => Tag::query()->where('user_id', $request->user()->id)->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, Lead $lead): JsonResponse|RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Lead::statuses()))],
        ]);

        $from = $lead->status;
        $to = $validated['status'];
        $wasOutsidePipeline = ! $lead->is_in_pipeline;

        if ($from !== $to || $wasOutsidePipeline) {
            $lead->update([
                'status' => $to,
                'is_in_pipeline' => true,
                'pipeline_entered_at' => $lead->pipeline_entered_at ?? now(),
            ]);

            if ($from !== $to) {
                LeadActivity::logStatusChanged($lead, $from, $to, $request->user());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'status' => $lead->status,
                'label' => Lead::statuses()[$lead->status]['label'] ?? ucfirst($lead->status),
            ]);
        }

        return redirect()->route('user.pipeline.index')->with('success', __('Pipeline updated.'));
    }

    public function remove(Request $request, Lead $lead): JsonResponse|RedirectResponse
    {
        $this->authorizeOwnership($request, $lead);

        if ($lead->is_in_pipeline) {
            $lead->update(['is_in_pipeline' => false]);
            LeadActivity::logPipelineRemoved($lead, $request->user());
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('user.pipeline.index')->with('success', __('Lead removed from pipeline.'));
    }

    protected function authorizeOwnership(Request $request, Lead $lead): void
    {
        abort_unless($lead->user_id === $request->user()->id, 404);
    }
}
