<?php

namespace App\Support;

class SocialLoginConfig
{
    public const PROVIDERS = ['google', 'facebook', 'github'];

    public static function supported(string $provider): bool
    {
        return in_array($provider, self::PROVIDERS, true);
    }

    public static function enabled(string $provider): bool
    {
        return self::supported($provider) && (bool) setting("social_{$provider}_enabled", false);
    }

    public static function clientId(string $provider): string
    {
        $setting = trim((string) setting("social_{$provider}_client_id", ''));

        return $setting !== '' ? $setting : trim((string) config("services.{$provider}.client_id", ''));
    }

    public static function clientSecret(string $provider): string
    {
        $setting = trim((string) setting("social_{$provider}_client_secret", ''));

        return $setting !== '' ? $setting : trim((string) config("services.{$provider}.client_secret", ''));
    }

    public static function configured(string $provider): bool
    {
        return self::enabled($provider)
            && self::clientId($provider) !== ''
            && self::clientSecret($provider) !== '';
    }

    public static function apply(string $provider): void
    {
        if (! self::configured($provider)) {
            return;
        }

        config([
            "services.{$provider}.client_id" => self::clientId($provider),
            "services.{$provider}.client_secret" => self::clientSecret($provider),
            "services.{$provider}.redirect" => route('social.callback', $provider),
        ]);
    }
}
