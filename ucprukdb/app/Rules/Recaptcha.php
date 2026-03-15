<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post(config('recaptcha.verify_url'), [
            'secret' => config('recaptcha.secret_key'),
            'response' => $value,
        ]);

        $success = $response->successful() && $response->json('success') === true;

        if (!$success) {
            \Log::error('reCAPTCHA Validation Failed', [
                'error_codes' => $response->json('error-codes'),
                'site_key_prefix' => substr(config('recaptcha.site_key'), 0, 10) . '...',
                'ip' => request()->ip(),
            ]);
            $fail('Verifikasi captcha gagal. Silakan coba lagi.');
        }
    }
}

