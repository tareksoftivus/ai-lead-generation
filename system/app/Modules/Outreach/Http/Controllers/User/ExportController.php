<?php

namespace App\Modules\Outreach\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\Outreach\Models\LeadExport;
use App\Modules\Outreach\Services\LeadExportService;
use App\Modules\Outreach\Services\LeadSourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ExportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:outreach.view', only: ['index', 'download']),
            new Middleware('permission:outreach.manage', except: ['index', 'download']),
        ];
    }

    public function __construct(
        protected LeadExportService $exports,
        protected LeadSourceService $sources
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $selectedIds = $this->selectedIds($request);
        $selectedCount = $selectedIds === []
            ? 0
            : Lead::query()->forUser($user->id)->whereKey($selectedIds)->count();

        $lists = LeadList::query()->forUser($user->id)->withCount('leads')->orderBy('name')->get();
        $searchRuns = SearchRun::query()->forUser($user->id)->with('search')->where('results_count', '>', 0)->latest()->limit(20)->get();
        $allCount = Lead::query()->forUser($user->id)->count();

        return view('outreach::user.export.index', [
            'columns' => $this->exports->columns(),
            'exports' => LeadExport::query()->forUser($user->id)->latest()->limit(20)->get(),
            'lists' => $lists,
            'searchRuns' => $searchRuns,
            'allCount' => $allCount,
            'selectedIds' => $selectedIds,
            'selectedCount' => $selectedCount,
            'supportsXlsx' => class_exists(ZipArchive::class),
        ]);
    }

    public function store(Request $request): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        $data = $this->validateExport($request);
        $selectedIds = $this->selectedIds($request);
        $count = $this->sources->count(
            $request->user(),
            $data['source_type'],
            $data['source_id'] ?? null,
            $selectedIds,
            $request->boolean('require_email')
        );

        if ($count < 1) {
            return back()->withInput()->with('error', __('No leads match that export.'));
        }

        $export = $this->exports->create(
            $request->user(),
            array_merge($data, ['require_email' => $request->boolean('require_email')]),
            $request->input('columns', []),
            $selectedIds
        );

        Log::info('Lead export created.', [
            'user_id' => $request->user()->id,
            'export_id' => $export->id,
            'rows_count' => $export->rows_count,
            'format' => $export->format,
        ]);

        return $this->exports->response($request->user(), $export, $selectedIds);
    }

    public function download(Request $request, LeadExport $leadExport): StreamedResponse|BinaryFileResponse
    {
        $this->authorizeOwnership($request, $leadExport);

        return $this->exports->response($request->user(), $leadExport);
    }

    public function destroy(Request $request, LeadExport $leadExport): RedirectResponse
    {
        $this->authorizeOwnership($request, $leadExport);
        $leadExport->delete();

        return redirect()->route('user.export.index')->with('success', __('Export deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateExport(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'source_type' => ['required', Rule::in([
                LeadSourceService::SOURCE_ALL,
                LeadSourceService::SOURCE_SELECTION,
                LeadSourceService::SOURCE_LIST,
                LeadSourceService::SOURCE_SEARCH,
            ])],
            'source_id' => ['nullable', 'integer'],
            'selected_ids' => ['nullable', 'string'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['string', Rule::in(array_keys($this->exports->columns()))],
            'format' => ['required', Rule::in(['csv', 'xlsx'])],
            'require_email' => ['nullable', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($request->input('format') === 'xlsx' && ! class_exists(ZipArchive::class)) {
                $validator->errors()->add('format', __('XLSX export needs the PHP zip extension. Please export CSV for now.'));
            }

            $type = $request->input('source_type');
            $sourceId = $request->integer('source_id');

            if ($type === LeadSourceService::SOURCE_SELECTION && $this->selectedIds($request) === []) {
                $validator->errors()->add('selected_ids', __('Select at least one lead, or export every lead instead.'));
            }

            if ($type === LeadSourceService::SOURCE_LIST) {
                $exists = LeadList::query()->forUser($request->user()->id)->whereKey($sourceId)->exists();
                if (! $exists) {
                    $validator->errors()->add('source_id', __('Choose a valid lead list.'));
                }
            }

            if ($type === LeadSourceService::SOURCE_SEARCH) {
                $exists = SearchRun::query()->forUser($request->user()->id)->whereKey($sourceId)->exists();
                if (! $exists) {
                    $validator->errors()->add('source_id', __('Choose a valid search.'));
                }
            }
        });

        return $validator->validate();
    }

    /**
     * @return array<int, int>
     */
    protected function selectedIds(Request $request): array
    {
        $source = $request->input('selected_ids', $request->query('ids', ''));
        $ids = is_array($source) ? $source : explode(',', (string) $source);

        return collect($ids)
            ->map(fn (mixed $id): int => (int) trim((string) $id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function authorizeOwnership(Request $request, LeadExport $export): void
    {
        abort_unless($export->user_id === $request->user()->id, 404);
    }
}
