# Usage Examples

## Example 1: Contact Form Resource

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(1000)
                    ->rows(5),

                ReCaptcha::make('recaptcha')
                    ->theme('light')
                    ->size('normal'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateContact::class,
        ];
    }
}
```

## Example 2: Public Customer Registration Form (No Authentication)

```php
<?php

namespace App\Livewire;

use App\Models\Customer;
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CustomerRegistrationForm extends Component implements HasForms
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
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(Customer::class, 'email')
                    ->maxLength(255),

                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                TextInput::make('license_plate')
                    ->required()
                    ->maxLength(20)
                    ->alphaNum(),

                ReCaptcha::make('recaptcha')
                    ->theme('light'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Remove recaptcha from data
        unset($data['recaptcha']);

        // Create customer
        $customer = Customer::create($data);

        // Send welcome email
        // Mail::to($customer->email)->send(new WelcomeEmail($customer));

        session()->flash('success', 'Registration successful! Welcome aboard.');

        $this->form->fill();
        $this->dispatch('resetRecaptcha');

        $this->redirect('/customer/dashboard');
    }

    protected function onValidationError(ValidationException $exception): void
    {
        parent::onValidationError($exception);

        // Reset reCAPTCHA on validation errors
        $this->dispatch('resetRecaptcha');
    }

    public function render()
    {
        return view('livewire.customer-registration-form');
    }
}
```

### Corresponding Blade View

```blade
{{-- resources/views/livewire/customer-registration-form.blade.php --}}
<div class="mx-auto max-w-2xl">
    <div class="rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">
            Customer Registration
        </h2>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6">
                <button
                    type="submit"
                    class="w-full rounded-lg bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-500 dark:hover:bg-primary-600"
                >
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
```

## Example 3: Multi-Step Form with reCAPTCHA on Final Step

```php
<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = BookingResource::class;

    protected function getSteps(): array
    {
        return [
            Wizard\Step::make('Customer Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->required(),
                ]),

            Wizard\Step::make('Vehicle Information')
                ->schema([
                    TextInput::make('license_plate')
                        ->required()
                        ->maxLength(20),

                    TextInput::make('vehicle_type')
                        ->required(),

                    TextInput::make('vehicle_color')
                        ->maxLength(50),
                ]),

            Wizard\Step::make('Booking Details')
                ->schema([
                    DateTimePicker::make('start_time')
                        ->required()
                        ->minDate(now()),

                    DateTimePicker::make('end_time')
                        ->required()
                        ->after('start_time'),

                    Textarea::make('notes')
                        ->maxLength(500),
                ]),

            Wizard\Step::make('Confirmation')
                ->schema([
                    ReCaptcha::make('recaptcha')
                        ->theme('light'),
                ]),
        ];
    }

    protected function onValidationError(ValidationException $exception): void
    {
        parent::onValidationError($exception);
        $this->dispatch('resetRecaptcha');
    }
}
```

## Example 4: Dark Theme reCAPTCHA

```php
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;

ReCaptcha::make('recaptcha')
    ->theme('dark')
    ->size('normal')
```

## Example 5: Compact reCAPTCHA (for mobile-friendly forms)

```php
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;

ReCaptcha::make('recaptcha')
    ->theme('light')
    ->size('compact')
```

## Example 6: Testing with HTTP Faking

```php
<?php

use App\Livewire\ContactForm;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('can submit contact form with valid recaptcha', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
            'challenge_ts' => now()->toIso8601String(),
            'hostname' => 'localhost',
        ], 200)
    ]);

    Livewire::test(ContactForm::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'recaptcha' => 'valid-test-token',
        ])
        ->call('submit')
        ->assertHasNoFormErrors();
});

it('cannot submit contact form with invalid recaptcha', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200)
    ]);

    Livewire::test(ContactForm::class)
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
            'recaptcha' => 'invalid-token',
        ])
        ->call('submit')
        ->assertHasFormErrors(['recaptcha']);
});
```

## Example 7: Integration with Parking Public Customer

```php
<?php

namespace App\Filament\PublicCustomer\Pages;

use App\Models\Ticket;
use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;

class PayTicket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.public-customer.pages.pay-ticket';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticket_number')
                    ->label('Ticket Number')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('Enter your ticket number'),

                ReCaptcha::make('recaptcha')
                    ->theme('light')
                    ->size('normal'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $ticket = Ticket::where('number', $data['ticket_number'])
            ->whereNull('paid_at')
            ->firstOrFail();

        // Redirect to payment page
        $this->redirect(route('public-customer.payment', ['ticket' => $ticket]));
    }

    protected function onValidationError(ValidationException $exception): void
    {
        parent::onValidationError($exception);
        $this->dispatch('resetRecaptcha');
    }
}
```

## Tips for Best Practices

1. **Always reset reCAPTCHA on validation errors** to allow users to try again
2. **Remove the recaptcha field from data** before saving to database
3. **Use HTTP faking in tests** to avoid making real API calls to Google
4. **Choose theme based on your app's design** - light for light themes, dark for dark themes
5. **Use compact size on mobile forms** to save space on smaller screens
6. **Monitor logs regularly** to catch configuration issues or bot attacks
7. **Test token expiration** by keeping a form open for 3+ minutes before submitting
