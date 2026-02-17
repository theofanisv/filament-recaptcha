<?php

use Theograms\FilamentRecaptcha\Rules\ReCaptchaRule;
use Illuminate\Support\Facades\Http;

it('passes validation with valid token', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
            'challenge_ts' => now()->toIso8601String(),
            'hostname' => 'localhost',
        ], 200)
    ]);

    $rule = new ReCaptchaRule();
    $failed = false;
    $rule->validate('recaptcha', 'valid-token', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('fails validation with invalid token', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200)
    ]);

    $rule = new ReCaptchaRule();
    $message = null;
    $rule->validate('recaptcha', 'invalid-token', function ($msg) use (&$message) {
        $message = $msg;
    });

    expect($message)->not->toBeNull();
});

it('fails validation with empty token', function () {
    $rule = new ReCaptchaRule();
    $message = null;
    $rule->validate('recaptcha', '', function ($msg) use (&$message) {
        $message = $msg;
    });

    expect($message)->not->toBeNull();
});

it('fails validation when API request fails', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([], 500)
    ]);

    $rule = new ReCaptchaRule();
    $message = null;
    $rule->validate('recaptcha', 'some-token', function ($msg) use (&$message) {
        $message = $msg;
    });

    expect($message)->not->toBeNull();
});

it('returns appropriate error message on empty token', function () {
    $rule = new ReCaptchaRule();
    $message = null;
    $rule->validate('recaptcha', '', function ($msg) use (&$message) {
        $message = $msg;
    });

    expect($message)->toBeString();
});
