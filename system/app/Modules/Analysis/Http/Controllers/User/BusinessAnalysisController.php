<?php

namespace App\Modules\Analysis\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Analysis\Http\Requests\RunBusinessAnalysisRequest;
use App\Modules\Analysis\Models\BusinessAnalysisRun;
use App\Modules\Analysis\Services\BusinessAnalysisService;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Services\CreditLedger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAnalysisController extends Controller
{
    public function __construct(
        protected BusinessAnalysisService $service,
        protected CreditLedger $ledger
    ) {}

    public function index(Request $request): View
    {
        return view('analysis::user.index', $this->service->dashboardData(
            $request->user(),
            $request->string('source')->toString() ?: null
        ) + [
            'balance' => $this->ledger->balance($request->user()),
            'focus' => BusinessAnalysisRun::FOCUS_GAPS,
        ]);
    }

    public function run(RunBusinessAnalysisRequest $request): RedirectResponse
    {
        try {
            $run = $this->service->run(
                $request->user(),
                (int) $request->validated('source'),
                (string) $request->validated('focus'),
                (bool) $request->validated('skip_analysed')
            );
        } catch (InsufficientCreditsException) {
            return back()
                ->withInput()
                ->with('error', __('You dont have sufficient credits please upgrade your plan'));
        }

        if ($run->businesses_count === 0) {
            $message = $run->leadList?->leads()->exists()
                ? __('Everything in that list has already been analysed.')
                : __('That list has no businesses yet. Add leads to it before running analysis.');

            return redirect()
                ->route('user.analysis.index', ['source' => $run->lead_list_id])
                ->with('success', $message);
        }

        return redirect()
            ->route('user.analysis.index', ['source' => $run->lead_list_id])
            ->with('success', __('Analysis complete. :count businesses read, :credits credits spent.', [
                'count' => number_format($run->businesses_count),
                'credits' => number_format($run->credits_spent),
            ]));
    }
}
