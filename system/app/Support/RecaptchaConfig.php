<?php

namespace App\Support;

class RecaptchaConfig
{
    public static function enabled(): bool
    {
        return (bool) setting('plugin_turnstile_enabled', false);
    }

    public static function siteKey(): string
    {
        return trim((string) setting('plugin_turnstile_site_key', ''));
    }

    public static function secretKey(): string
    {
        return trim((string) setting('plugin_turnstile_secret_key', ''));
    }

    public static function hasValidSiteKey(): bool
    {
        $siteKey = self::siteKey();

        return $siteKey !== '' && preg_match('/^[A-Za-z0-9_-]{20,80}$/', $siteKey) === 1;
    }

    public static function renderable(): bool
    {
        return self::enabled() && self::hasValidSiteKey();
    }

    public static function verifiable(): bool
    {
        return self::renderable() && self::secretKey() !== '';
    }
}
