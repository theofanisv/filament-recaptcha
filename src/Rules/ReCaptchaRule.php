<?php

namespace Theograms\FilamentRecaptcha\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptchaRule implements Rule
{
    protected ?string $message = null;

    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            $this->message = __('Please complete the reCAPTCHA verification.');
            return false;
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
                $this->message = __('Unable to verify reCAPTCHA. Please try again.');
                return false;
            }

            $result = $response->json();

            if (!($result['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'errors' => $result['error-codes'] ?? []
                ]);
                $this->message = __('reCAPTCHA verification failed. Please try again.');
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA exception: ' . $e->getMessage());
            $this->message = __('An error occurred during verification. Please try again.');
            return false;
        }
    }

    public function message(): string
    {
        return $this->message ?? __('The reCAPTCHA verification failed.');
    }
}
