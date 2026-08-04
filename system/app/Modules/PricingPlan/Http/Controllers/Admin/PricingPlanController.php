<?php

namespace App\Modules\PricingPlan\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\PricingPlan\Http\Requests\StorePricingPlanRequest;
use App\Modules\PricingPlan\Http\Requests\UpdatePricingPlanRequest;
use App\Modules\PricingPlan\Services\PricingPlanService;
use App\Modules\PricingPlan\Tables\PricingPlansTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class PricingPlanController extends Controller implements HasMiddleware
{
    use HasCrudActions;

    protected string $viewPath = 'pricing-plans::admin';

    protected string $routePrefix = 'admin.pricing-plans';

    protected string $resourceName = 'pricing_plans';

    public static function middleware(): array
    {
        return static::crudMiddleware('pricing-plans');
    }

    public function __construct(protected PricingPlanService $service) {}

    public function create(): View
    {
        return view("{$this->viewPath}.create", array_merge(
            ['pricingPlan' => null],
            $this->formData()
        ));
    }

    public function store(StorePricingPlanRequest $request): RedirectResponse
    {
        $this->service->create($this->withParsedFeatures($request->validated()));

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Pricing plan created successfully.'));
    }

    public function update(UpdatePricingPlanRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $this->service->update($record, $this->withParsedFeatures($request->validated()));

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Pricing plan updated successfully.'));
    }

    /**
     * Convert the newline-separated features textarea into an array.
     */
    protected function withParsedFeatures(array $data): array
    {
        $data['features'] = collect(explode("\n", (string) ($data['features'] ?? '')))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $data;
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return PricingPlansTable::make();
    }

    protected function getSingularVariable(): string
    {
        return 'pricingPlan';
    }
}
