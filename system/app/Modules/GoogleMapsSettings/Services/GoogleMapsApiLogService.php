<?php

namespace App\Modules\GoogleMapsSettings\Services;

use App\Modules\GoogleMapsSettings\Models\GoogleMapsApiLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleMapsApiLogService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function record(array $attributes): void
    {
        try {
            GoogleMapsApiLog::query()->create([
                'action' => $attributes['action'],
                'method' => $attributes['method'],
                'url' => $attributes['url'],
                'request_payload' => $this->sanitize($attributes['request_payload'] ?? null),
                'status_code' => $attributes['status_code'] ?? null,
                'successful' => (bool) ($attributes['successful'] ?? false),
                'attempts' => $attributes['attempts'] ?? 1,
                'duration_ms' => $attributes['duration_ms'] ?? null,
                'response_body' => $this->summarizeResponse($attributes['response_body'] ?? null),
                'error_message' => $attributes['error_message'] ?? null,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Unable to write Google Maps API log.', [
                'action' => $attributes['action'] ?? null,
                'status_code' => $attributes['status_code'] ?? null,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    protected function sanitize(mixed $payload): mixed
    {
        if (! is_array($payload)) {
            return $payload;
        }

        return Arr::except($payload, [
            'key',
            'api_key',
            'apikey',
            'password',
            'secret',
            'token',
        ]);
    }

    protected function summarizeResponse(mixed $response): mixed
    {
        if (! is_array($response)) {
            return $response;
        }

        if (isset($response['places']) && is_array($response['places'])) {
            return [
                'places_count' => count($response['places']),
                'nextPageToken' => $response['nextPageToken'] ?? null,
                'places' => array_slice($response['places'], 0, 5),
            ];
        }

        return $response;
    }
}
