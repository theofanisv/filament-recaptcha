<?php

return [
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Site Key
    |--------------------------------------------------------------------------
    |
    | The reCAPTCHA site key for your application. This is displayed on
    | the client-side to render the reCAPTCHA widget.
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Secret Key
    |--------------------------------------------------------------------------
    |
    | The reCAPTCHA secret key for your application. This is used on the
    | server-side to verify the reCAPTCHA response.
    |
    */

    'secret_key' => env('RECAPTCHA_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Version
    |--------------------------------------------------------------------------
    |
    | The version of reCAPTCHA to use. Currently supports 'v2' only.
    |
    */

    'version' => env('RECAPTCHA_VERSION', 'v2'),

    /*
    |--------------------------------------------------------------------------
    | Default reCAPTCHA Type
    |--------------------------------------------------------------------------
    |
    | The default type of reCAPTCHA widget to use. Options:
    | - 'checkbox': Displays the "I'm not a robot" checkbox (default)
    | - 'invisible': Invisible reCAPTCHA badge that triggers automatically
    |
    */

    'default_type' => env('RECAPTCHA_DEFAULT_TYPE', 'checkbox'),

    /*
    |--------------------------------------------------------------------------
    | Default Badge Position (Invisible Only)
    |--------------------------------------------------------------------------
    |
    | For invisible reCAPTCHA, where to position the badge. Options:
    | - 'bottomright' (default)
    | - 'bottomleft'
    | - 'inline'
    |
    */

    'default_badge_position' => env('RECAPTCHA_DEFAULT_BADGE_POSITION', 'bottomright'),
];