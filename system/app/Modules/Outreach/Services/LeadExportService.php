<?php

namespace App\Modules\Outreach\Services;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use App\Modules\Outreach\Models\LeadExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class LeadExportService
{
    public function __construct(protected LeadSourceService $sources) {}

    /**
     * @return array<string, array{label: string, note: string, default: bool, locked?: bool}>
     */
    public function columns(): array
    {
        return [
            'business' => ['label' => 'Business name', 'note' => 'Always included', 'default' => true, 'locked' => true],
            'category' => ['label' => 'Category', 'note' => 'Google category', 'default' => true],
            'address' => ['label' => 'Address', 'note' => 'Street, city, postcode, country', 'default' => true],
            'phone' => ['label' => 'Phone', 'note' => 'As listed', 'default' => true],
            'email' => ['label' => 'Email', 'note' => 'Blank where none found', 'default' => true],
            'website' => ['label' => 'Website', 'note' => 'Domain and full URL', 'default' => true],
            'score' => ['label' => 'Lead score', 'note' => 'The number, 0-100', 'default' => true],
            'reasoning' => ['label' => 'Why it scored that', 'note' => 'The written reasoning', 'default' => true],
            'status' => ['label' => 'Status', 'note' => 'New, contacted, qualified...', 'default' => true],
            'tags' => ['label' => 'Tags', 'note' => 'Comma separated', 'default' => false],
            'reviews' => ['label' => 'Reviews', 'note' => 'Count and rating', 'default' => false],
            'coords' => ['label' => 'Coordinates', 'note' => 'Latitude and longitude', 'default' => false],
        ];
    }

    /**
     * @param  array<int, string>  $requestedColumns
     * @param  array<int, int|string>  $selectedIds
     */
    public function create(User $user, array $data, array $requestedColumns, array $selectedIds = []): LeadExport
    {
        $sourceType = $data['source_type'] ?? LeadSourceService::SOURCE_ALL;
        $sourceId = isset($data['source_id']) ? (int) $data['source_id'] : null;
        $format = $data['format'] ?? 'csv';
        $requireEmail = (bool) ($data['require_email'] ?? false);
        $columns = $this->normalizeColumns($requestedColumns);
        $rows = $this->sources->count($user, $sourceType, $sourceId, $selectedIds, $requireEmail);
        $label = $this->sources->label($user, $sourceType, $sourceId, count($selectedIds));
        $filename = $this->filename($label, $format);

        return LeadExport::query()->create([
            'user_id' => $user->id,
            'filename' => $filename,
            'format' => $format,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_label' => $label,
            'columns' => $columns,
            'selected_lead_ids' => array_values(array_map('intval', $selectedIds)),
            'require_email' => $requireEmail,
            'rows_count' => $rows,
            'columns_count' => count($columns),
            'downloaded_at' => now(),
        ]);
    }

    /**
     * @param  array<int, int|string>  $selectedIds
     */
    public function response(User $user, LeadExport $export, array $selectedIds = []): StreamedResponse|BinaryFileResponse
    {
        $selectedIds = $selectedIds ?: ($export->selected_lead_ids ?? []);
        $leads = $this->sources
            ->query($user, $export->source_type, $export->source_id, $selectedIds, $export->require_email)
            ->get();

        $export->update(['downloaded_at' => now()]);

        if ($export->format === 'xlsx' && class_exists(ZipArchive::class)) {
            return $this->xlsxResponse($export, $leads);
        }

        return $this->csvResponse($export, $leads);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    protected function normalizeColumns(array $columns): array
    {
        $available = array_keys($this->columns());
        $chosen = array_values(array_intersect($columns, $available));

        if (! in_array('business', $chosen, true)) {
            array_unshift($chosen, 'business');
        }

        return array_values(array_unique($chosen));
    }

    protected function filename(string $label, string $format): string
    {
        return Str::slug($label ?: 'leads').'-'.now()->format('Y-m-d-His').'.'.$format;
    }

    /**
     * @param  Collection<int, Lead>  $leads
     */
    protected function csvResponse(LeadExport $export, Collection $leads): StreamedResponse
    {
        return response()->streamDownload(function () use ($export, $leads): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $this->headers($export->columns));

            foreach ($leads as $lead) {
                fputcsv($handle, $this->row($lead, $export->columns));
            }

            fclose($handle);
        }, Str::replaceEnd('.xlsx', '.csv', $export->filename), [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  Collection<int, Lead>  $leads
     */
    protected function xlsxResponse(LeadExport $export, Collection $leads): BinaryFileResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'lead-export-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypes());
        $zip->addFromString('_rels/.rels', $this->xlsxRels());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRels());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxSheet($export, $leads));
        $zip->close();

        return response()->download($path, $export->filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    protected function headers(array $columns): array
    {
        $available = $this->columns();

        return array_map(fn (string $column): string => $available[$column]['label'] ?? Str::headline($column), $columns);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    protected function row(Lead $lead, array $columns): array
    {
        return array_map(fn (string $column): string => $this->value($lead, $column), $columns);
    }

    protected function value(Lead $lead, string $column): string
    {
        $place = $lead->place;

        return match ($column) {
            'business' => (string) $place?->name,
            'category' => (string) $place?->google_category,
            'address' => (string) $place?->formatted_address,
            'phone' => (string) $place?->phone,
            'email' => (string) $lead->email,
            'website' => (string) $place?->website,
            'score' => (string) $lead->score,
            'reasoning' => (string) data_get($lead->score_signals, 'reasoning', data_get($lead->score_signals, 'summary', '')),
            'status' => (string) $lead->status,
            'tags' => $lead->tags->pluck('name')->implode(', '),
            'reviews' => trim(((string) $place?->rating).' / '.((string) $place?->review_count), ' /'),
            'coords' => $place && $place->lat !== null && $place->lng !== null ? $place->lat.', '.$place->lng : '',
            default => '',
        };
    }

    /**
     * @param  Collection<int, Lead>  $leads
     */
    protected function xlsxSheet(LeadExport $export, Collection $leads): string
    {
        $rows = [$this->headers($export->columns)];

        foreach ($leads as $lead) {
            $rows[] = $this->row($lead, $export->columns);
        }

        $xmlRows = collect($rows)->map(function (array $row, int $index): string {
            $cells = collect($row)->map(function (string $value, int $columnIndex) use ($index): string {
                $cell = $this->xlsxCellName($columnIndex + 1).($index + 1);

                return '<c r="'.$cell.'" t="inlineStr"><is><t>'.e($value).'</t></is></c>';
            })->implode('');

            return '<row r="'.($index + 1).'">'.$cells.'</row>';
        })->implode('');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$xmlRows.'</sheetData></worksheet>';
    }

    protected function xlsxCellName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    protected function xlsxContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>';
    }

    protected function xlsxRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    protected function xlsxWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Leads" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    protected function xlsxWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>';
    }
}
