# Filament reCAPTCHA v2 Plugin

A production-ready Google reCAPTCHA v2 form component for Filament v4 supporting both **Checkbox** and **Invisible** modes.

> [!NOTE]  
> This package is AI-generated!

## Features

- ✅ Full Filament v4 integration with unified schemas
- ✅ **Checkbox reCAPTCHA** - Traditional "I'm not a robot" checkbox
- ✅ **Invisible reCAPTCHA** - Seamless bot protection without user interaction
- ✅ Alpine.js reactive integration with Livewire
- ✅ Server-side validation for bulletproof security
- ✅ Automatic token refresh on validation errors
- ✅ Support for light/dark themes (checkbox mode)
- ✅ Support for normal/compact sizes (checkbox mode)
- ✅ Configurable badge position (invisible mode)
- ✅ Comprehensive error handling and logging

## Installation

### 1. Install the package

Since this is a local package, add it to your main `composer.json`:

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

Then require the package:

```bash
composer require theofanisv/filament-recaptcha
```

### 2. Obtain reCAPTCHA keys

1. Visit https://www.google.com/recaptcha/admin/create
2. Select "reCAPTCHA v2" → "I'm not a robot" checkbox
3. Add your authorized domains (include `localhost` for development)
4. Copy your Site Key and Secret Key

### 3. Configure environment variables

Add to your `.env` file:

```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### 4. Publish configuration (optional)

```bash
php artisan vendor:publish --tag="filament-recaptcha-config"
```

## Usage

### Basic Usage

```php
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Components\TextInput;

// In your Filament resource or form
public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required(),

            TextInput::make('email')
                ->email()
                ->required(),

            ReCaptcha::make('recaptcha'),
        ]);
}
```

### Checkbox reCAPTCHA (Default)

The traditional "I'm not a robot" checkbox that users click:

```php
ReCaptcha::make('recaptcha')
    ->theme('dark')  // 'light' or 'dark' (default: 'light')
    ->size('compact'), // 'normal' or 'compact' (default: 'normal')
```

### Invisible reCAPTCHA

The invisible mode provides seamless bot protection without requiring user interaction. It automatically triggers when the form is submitted:

```php
ReCaptcha::make('recaptcha')
    ->invisible()  // Enable invisible mode
    ->badge('bottomright'), // Badge position: 'bottomright', 'bottomleft', or 'inline'
```

**Key differences from checkbox mode:**
- No visible widget - users don't need to click anything
- Automatically executes on form submission
- Shows a small badge in the corner (required by Google)
- Better user experience for legitimate users
- Still provides strong bot protection

**Badge positioning:**
- `bottomright` (default) - Badge appears in bottom-right corner
- `bottomleft` - Badge appears in bottom-left corner
- `inline` - Badge appears inline with the form

**Example with all options:**

```php
ReCaptcha::make('recaptcha')
    ->invisible()
    ->badge('inline')
    ->formId('contact-form'), // Optional: specify form ID for auto-submit
```

### Usage in Livewire Components

```php
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class ContactForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->email()
                    ->required(),

                TextInput::make('message')
                    ->required(),

                ReCaptcha::make('recaptcha'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Remove recaptcha from data since it's not needed after validation
        unset($data['recaptcha']);

        // Your business logic here

        session()->flash('success', 'Form submitted successfully!');
        $this->form->fill();
        $this->dispatch('resetRecaptcha'); // Reset the captcha
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
```

### Resetting reCAPTCHA After Validation Errors

If you want to reset the reCAPTCHA widget after validation errors (recommended for better UX):

```php
use Illuminate\Validation\ValidationException;

protected function onValidationError(ValidationException $exception): void
{
    parent::onValidationError($exception);

    // Reset reCAPTCHA on validation errors
    $this->dispatch('resetRecaptcha');
}
```

## How It Works

### Client-Side

1. The component loads the Google reCAPTCHA API asynchronously
2. Alpine.js initializes the reCAPTCHA widget when ready
3. When the user completes the challenge, the response token is stored in Livewire state
4. On expiration or error, the widget automatically resets

### Server-Side

1. The form is submitted with the reCAPTCHA token
2. The `ReCaptchaRule` validation rule sends the token to Google's API
3. Google verifies the token and returns success/failure
4. If verification fails, the form shows an error message
5. All failures are logged for monitoring

## Security Features

- Server-side validation cannot be bypassed
- Tokens are verified with Google's API on every submission
- User IP address is included in verification for additional security
- Comprehensive error logging for monitoring attacks
- Automatic token expiration handling
- Graceful error handling with user-friendly messages

## Configuration

The configuration file (`config/filament-recaptcha.php`) contains:

```php
return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'version' => env('RECAPTCHA_VERSION', 'v2'),
];
```

## Testing

When testing forms with reCAPTCHA, you can:

1. Use HTTP faking to mock Google's API responses
2. Whitelist testing IPs in development environments
3. Use test keys provided by Google

Example test:

```php
use Illuminate\Support\Facades\Http;

it('validates recaptcha token', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
        ], 200)
    ]);

    // Your test logic here
});
```

## Troubleshooting

### reCAPTCHA widget not showing

- Ensure your site key is correct in the `.env` file
- Check browser console for JavaScript errors
- Verify your domain is authorized in Google reCAPTCHA admin

### Validation always failing

- Verify your secret key is correct in the `.env` file
- Check Laravel logs for specific error messages
- Ensure your server can reach Google's API (no firewall blocking)

### Token expiring before submission

- The automatic reset mechanism handles this
- Users can simply complete the challenge again
- Consider implementing the `resetRecaptcha` event on validation errors

## Requirements

- PHP 8.3+
- Laravel 12+
- Filament 4+
- Active internet connection for Google reCAPTCHA API

## License

MIT License

## Credits

Based on the comprehensive guide: "Building a production-ready reCAPTCHA v2 plugin for Filament v4"
