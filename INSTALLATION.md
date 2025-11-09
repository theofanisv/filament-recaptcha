# Installation Guide

This guide will walk you through installing and configuring the Filament reCAPTCHA plugin.

## Step 1: Register the Package with Composer

Add the following to your main `composer.json` file in the root of your project (not the plugin's composer.json):

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/theofanisv/filament-recaptcha"
        }
    ]
}
```

## Step 2: Require the Package

Run the following command from your project root:

```bash
composer require theofanisv/filament-recaptcha
```

If you encounter any issues, try:

```bash
composer require theofanisv/filament-recaptcha --no-interaction
```

## Step 3: Get reCAPTCHA Keys

1. Visit https://www.google.com/recaptcha/admin/create
2. Fill in the form:
   - **Label**: System
   - **reCAPTCHA type**: reCAPTCHA v2 → "I'm not a robot" Checkbox
   - **Domains**:
     - `localhost` (for local development)
     - Your production domain(s)
3. Accept the terms and click Submit
4. Copy your **Site Key** and **Secret Key**

## Step 4: Configure Environment Variables

Add the following to your `.env` file:

```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_VERSION=v2
```

Also update `.env.bak` (your template file) with placeholder values:

```env
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

## Step 5: Publish Configuration (Optional)

If you want to customize the configuration:

```bash
php artisan vendor:publish --tag="filament-recaptcha-config"
```

This will create `config/filament-recaptcha.php` in your main application.

## Step 6: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
```

## Step 7: Usage in Your Application

### For Public Customer Forms (No Authentication Required)

Perfect for forms like:
- Ticket payment lookup
- Subscription renewal
- Customer registration
- Contact forms

Example:

```php
<?php

namespace App\Filament\PublicCustomer\Pages;

use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class PayTicket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.public-customer.pages.pay-ticket';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticket_number')
                    ->label('Ticket Number')
                    ->required(),

                ReCaptcha::make('recaptcha'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Remove recaptcha from data
        unset($data['recaptcha']);

        // Your logic here
    }

    protected function onValidationError(\Illuminate\Validation\ValidationException $exception): void
    {
        parent::onValidationError($exception);
        $this->dispatch('resetRecaptcha');
    }
}
```

### For Admin Panel Forms (With Authentication)

You can also use it in admin resources:

```php
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Your other fields...

            ReCaptcha::make('recaptcha')
                ->theme('light')
                ->size('normal'),
        ]);
}
```

## Step 8: Testing

Run the plugin tests to ensure everything is working:

```bash
cd packages/filament-recaptcha
composer install
./vendor/bin/pest
```

## Common Use Cases

### 1. Public Ticket Payment Form

Add reCAPTCHA to prevent automated ticket lookups and spam.

### 2. Customer Self-Registration

Add reCAPTCHA to prevent bot registrations in your customer sign-up forms.

### 3. Contact/Support Forms

Protect your contact forms from spam submissions.

### 4. Subscription Renewal Forms

Add an extra layer of security when customers renew their subscriptions.

## Troubleshooting

### reCAPTCHA widget not showing

1. Check browser console for errors
2. Verify your site key is correct in `.env`
3. Ensure your domain is registered in Google reCAPTCHA admin
4. Clear browser cache and cookies

### Validation always failing

1. Check `storage/logs/laravel.log` for specific errors
2. Verify your secret key is correct in `.env`
3. Run `php artisan config:clear`
4. Ensure your server can reach Google's API (check firewall)

### Token expired errors

This is normal if users take longer than 2 minutes to complete the form. The automatic reset mechanism handles this - users just need to complete the challenge again.

Make sure you're dispatching the reset event:

```php
protected function onValidationError(\Illuminate\Validation\ValidationException $exception): void
{
    parent::onValidationError($exception);
    $this->dispatch('resetRecaptcha');
}
```

## Security Considerations

1. **Never expose your secret key** - it should only be in `.env` and server config
2. **Always validate server-side** - the component does this automatically
3. **Monitor your logs** - watch for unusual patterns in `storage/logs/laravel.log`
4. **Rate limit your forms** - combine reCAPTCHA with Laravel's throttle middleware

Example rate limiting:

```php
Route::middleware(['throttle:5,1'])->group(function () {
    // Public customer routes
});
```

## Performance Optimization

The reCAPTCHA script loads asynchronously and won't block your page load. The component uses:

- `async defer` script loading
- `wire:ignore` to prevent Livewire conflicts
- Polling-based initialization for reliability

## Support

For issues or questions:

1. Check the README.md and USAGE_EXAMPLES.md files
2. Review the tests for implementation examples
3. Check Laravel logs for detailed error messages
4. Pray

## Next Steps

After installation, see `USAGE_EXAMPLES.md` for comprehensive examples of using the plugin in various scenarios.
