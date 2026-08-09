<?php

namespace App\Modules\Leads\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * The concrete "enrichment" mechanic: a simple, bounded, timeout-guarded
 * scan of a business's website for a contact email. Intentionally crude —
 * not a real scraping/crawling system — isolated in its own class so a real
 * enrichment vendor (Hunter.io, Clearbit, etc.) can replace it later without
 * touching LeadEnrichmentService's calling contract.
 */
class EmailFromWebsiteLookup
{
    /**
     * @var array<string>
     */
    protected array $denylistDomains = [
        'example.com',
        'sentry.io',
        'wixpress.com',
        'schema.org',
        'w3.org',
    ];

    public function find(?string $website): ?string
    {
        if (! $website) {
            return null;
        }

        $email = $this->scan($website);

        if ($email) {
            return $email;
        }

        return $this->scan(rtrim($website, '/').'/contact');
    }

    protected function scan(string $url): ?string
    {
        try {
            $response = Http::timeout(5)->get($url);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $body = $response->body();

        if (preg_match('/mailto:([^"\'?\s]+)/i', $body, $matches)) {
            $email = $matches[1];

            if ($this->isPlausible($email)) {
                return $email;
            }
        }

        if (preg_match('/[\w.+-]+@[\w-]+\.[a-z]{2,}/i', $body, $matches)) {
            $email = $matches[0];

            if ($this->isPlausible($email)) {
                return $email;
            }
        }

        return null;
    }

    protected function isPlausible(string $email): bool
    {
        $domain = mb_strtolower(substr(strrchr($email, '@') ?: '', 1));

        return $domain !== '' && ! in_array($domain, $this->denylistDomains, true);
    }
}
