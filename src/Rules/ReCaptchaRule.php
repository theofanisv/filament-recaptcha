<?php

namespace Theograms\FilamentRecaptcha\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail(__('Please complete the reCAPTCHA verification.'));
            return;
        }

        try {
            $response = Http::timeout(10)->asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => config('filament-recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip()
                ]
            );

            if (!$response->successful()) {
                Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status()
                ]);
                $fail(__('Unable to verify reCAPTCHA. Please try again.'));
                return;
            }

            $result = $response->json();

            if (!($result['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'errors' => $result['error-codes'] ?? []
                ]);
                $fail(__('reCAPTCHA verification failed. Please try again.'));
                return;
            }

        } catch (\Exception $e) {
            Log::error('reCAPTCHA exception: ' . $e->getMessage());
            $fail(__('An error occurred during verification. Please try again.'));
        }
    }
}
