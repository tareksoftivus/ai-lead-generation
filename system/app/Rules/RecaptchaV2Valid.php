<?php

namespace App\Rules;

use App\Support\RecaptchaConfig;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

/**
 * Validates a Google reCAPTCHA v2 challenge response against siteverify.
 */
class RecaptchaV2Valid implements ValidationRule
{
    /**
     * Run even when the field is empty, so a missing challenge response is
     * rejected while reCAPTCHA is enabled and configured.
     */
    public bool $implicit = true;

    protected string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! RecaptchaConfig::verifiable()) {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail(__('Please complete the verification challenge.'));

            return;
        }

        try {
            $response = Http::asForm()->post($this->verifyUrl, [
                'secret' => RecaptchaConfig::secretKey(),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if ($response->json('success') !== true) {
                $fail(__('Verification failed. Please try again.'));
            }
        } catch (Throwable) {
            $fail(__('Could not verify the challenge. Please try again.'));
        }
    }
}
