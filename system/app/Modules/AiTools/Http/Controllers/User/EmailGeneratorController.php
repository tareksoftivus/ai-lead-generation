<?php

namespace App\Modules\AiTools\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\AiTools\Http\Requests\GenerateEmailDraftRequest;
use App\Modules\AiTools\Http\Requests\QueueEmailCampaignRequest;
use App\Modules\AiTools\Http\Requests\SaveEmailTemplateRequest;
use App\Modules\AiTools\Services\EmailGeneratorService;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailGeneratorController extends Controller
{
    public function __construct(protected EmailGeneratorService $emails) {}

    public function index(Request $request): View
    {
        return view('ai-tools::user.email.index', $this->emails->dashboardData(
            $request->user(),
            $request->integer('lead') ?: null,
            $request->integer('draft') ?: null
        ));
    }

    public function generate(GenerateEmailDraftRequest $request): RedirectResponse
    {
        try {
            $draft = $this->emails->generate($request->user(), $request->validated());
        } catch (InsufficientCreditsException) {
            return back()
                ->withInput()
                ->with('error', __('You dont have sufficient credits please upgrade your plan'));
        }

        return redirect()
            ->route('user.email.index', ['draft' => $draft->id, 'lead' => $draft->lead_id])
            ->with('success', __('Draft generated. You can edit it freely before using it.'));
    }

    public function storeTemplate(SaveEmailTemplateRequest $request): RedirectResponse
    {
        $template = $this->emails->saveTemplate($request->user(), $request->validated());

        return redirect()
            ->route('user.email.index', [
                'draft' => $request->integer('draft_id') ?: null,
                'lead' => $request->integer('lead_id') ?: null,
            ])
            ->with('success', __('Template ":name" saved.', ['name' => $template->name]));
    }

    public function queueCampaign(QueueEmailCampaignRequest $request): RedirectResponse
    {
        $campaign = $this->emails->queueCampaign($request->user(), $request->validated());

        return redirect()
            ->route('user.campaigns.index')
            ->with('success', __('Campaign ":name" is waiting for your review.', ['name' => $campaign->name]));
    }
}
