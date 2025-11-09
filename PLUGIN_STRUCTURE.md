# Filament reCAPTCHA Plugin - Complete Structure

This document provides an overview of the complete plugin structure and architecture.

## Directory Structure

```
packages/filament-recaptcha/
├── .gitignore                          # Git ignore file
├── CHANGELOG.md                        # Version history and changes
├── composer.json                       # Package dependencies and autoloading
├── INSTALLATION.md                     # Installation guide
├── phpunit.xml                         # PHPUnit configuration
├── README.md                           # Main documentation
├── USAGE_EXAMPLES.md                   # Comprehensive usage examples
│
├── config/
│   └── filament-recaptcha.php         # Configuration file (publishable)
│
├── resources/
│   └── views/
│       └── components/
│           └── recaptcha.blade.php    # Blade view template for component
│
├── src/
│   ├── Forms/
│   │   └── Components/
│   │       └── ReCaptcha.php          # Main form component class
│   ├── Rules/
│   │   └── ReCaptchaRule.php          # Server-side validation rule
│   ├── ReCaptchaPlugin.php            # Filament plugin class
│   └── ReCaptchaServiceProvider.php   # Laravel service provider
│
└── tests/
    ├── Feature/
    │   └── ReCaptchaComponentTest.php # Feature tests for component
    ├── Unit/
    │   └── ReCaptchaRuleTest.php      # Unit tests for validation rule
    ├── Pest.php                        # Pest configuration
    └── TestCase.php                    # Base test case
```

## File Descriptions

### Configuration & Build Files

#### `.gitignore`
- Ignores vendor/, node_modules/, IDE files, and logs
- Standard Laravel package gitignore

#### `composer.json`
- **Package name**: `theofanisv/filament-recaptcha`
- **Dependencies**:
  - PHP 8.3+
  - Filament 4.0+
  - Spatie Laravel Package Tools
- **Dev dependencies**: Pest, PHPUnit, Orchestra Testbench
- **Autoloading**: PSR-4 autoloading for `Theograms\FilamentRecaptcha` namespace

#### `phpunit.xml`
- PHPUnit configuration for running tests
- Test suite configuration
- Coverage settings

### Documentation Files

#### `README.md`
- Main plugin documentation
- Features overview
- Installation instructions
- Basic usage examples
- Configuration details
- Troubleshooting guide

#### `INSTALLATION.md`
- Step-by-step installation guide
- Environment setup
- Common use cases
- Security considerations

#### `USAGE_EXAMPLES.md`
- 7 comprehensive usage examples
- Contact forms
- Public customer forms
- Multi-step wizards
- Testing examples
- Best practices

#### `CHANGELOG.md`
- Version history
- Release notes
- Breaking changes (for future versions)

### Source Files

#### `config/filament-recaptcha.php`
**Purpose**: Configuration file for reCAPTCHA settings

**Contents**:
- Site key configuration
- Secret key configuration
- Version setting (v2)

**Usage**: Published to main app's config directory

---

#### `src/Forms/Components/ReCaptcha.php`
**Purpose**: Main form component class

**Key Features**:
- Extends `Filament\Forms\Components\Field`
- Configurable theme (light/dark)
- Configurable size (normal/compact)
- Auto-configured validation rule
- Retrieves site key from config

**Public Methods**:
- `theme(string | Closure $theme): static` - Set theme
- `size(string | Closure $size): static` - Set size
- `getTheme(): string` - Get current theme
- `getSize(): string` - Get current size
- `getSiteKey(): string` - Get site key from config

**Protected Methods**:
- `setUp(): void` - Configure component defaults

---

#### `src/Rules/ReCaptchaRule.php`
**Purpose**: Server-side validation rule

**Key Features**:
- Implements Laravel's `Rule` interface
- Verifies tokens with Google's API
- Comprehensive error handling
- Detailed logging for monitoring
- 10-second timeout for API requests
- Includes user IP in verification

**Public Methods**:
- `passes($attribute, $value): bool` - Validate reCAPTCHA token
- `message(): string` - Get validation error message

**Validation Flow**:
1. Check if token is empty
2. Send POST request to Google's siteverify endpoint
3. Check HTTP response status
4. Parse JSON response
5. Verify success flag
6. Log any errors
7. Return validation result

---

#### `src/ReCaptchaServiceProvider.php`
**Purpose**: Laravel service provider for package registration

**Key Features**:
- Extends Spatie's `PackageServiceProvider`
- Publishes configuration file
- Publishes views
- Provides install command

**Public Methods**:
- `configurePackage(Package $package): void` - Configure package
- `packageBooted(): void` - Post-boot hooks (currently empty)

---

#### `src/ReCaptchaPlugin.php`
**Purpose**: Filament plugin class for panel registration

**Key Features**:
- Implements Filament's `Plugin` interface
- Allows registration with Filament panels

**Public Methods**:
- `getId(): string` - Get plugin identifier
- `register(Panel $panel): void` - Register plugin with panel
- `boot(Panel $panel): void` - Boot plugin
- `make(): static` - Create plugin instance
- `get(): static` - Get plugin from container

---

#### `resources/views/components/recaptcha.blade.php`
**Purpose**: Blade template for rendering the reCAPTCHA widget

**Key Features**:
- Uses `x-dynamic-component` for field wrapper
- Alpine.js integration for widget management
- `wire:ignore` to prevent Livewire interference
- Automatic widget initialization
- Token expiration handling
- Error handling
- Reset capability via Livewire events

**Alpine.js Component Properties**:
- `state` - Entangled with Livewire state
- `widgetId` - Google reCAPTCHA widget ID
- `siteKey` - Site key from component
- `theme` - Theme setting
- `size` - Size setting

**Alpine.js Component Methods**:
- `init()` - Initialize component
- `waitForRecaptcha()` - Poll for reCAPTCHA API
- `renderWidget()` - Render the widget
- `reset()` - Reset the widget

**External Scripts**:
- Loads Google reCAPTCHA API asynchronously
- Uses `@once` directive to load only once per page

### Test Files

#### `tests/TestCase.php`
**Purpose**: Base test case for all tests

**Key Features**:
- Extends Orchestra Testbench
- Registers Filament and plugin service providers
- Sets up test environment
- Configures test database
- Sets test reCAPTCHA keys

---

#### `tests/Pest.php`
**Purpose**: Pest PHP configuration

**Contents**:
- Uses TestCase for all tests
- Configures test namespace

---

#### `tests/Unit/ReCaptchaRuleTest.php`
**Purpose**: Unit tests for validation rule

**Test Cases**:
1. ✅ Passes validation with valid token
2. ✅ Fails validation with invalid token
3. ✅ Fails validation with empty token
4. ✅ Fails validation when API request fails
5. ✅ Returns appropriate error message

**Features**:
- Uses HTTP faking to mock Google API
- Tests all success/failure scenarios
- Verifies error messaging

---

#### `tests/Feature/ReCaptchaComponentTest.php`
**Purpose**: Feature tests for component

**Test Cases**:
1. ✅ Can instantiate component
2. ✅ Has default theme of light
3. ✅ Can set theme to dark
4. ✅ Has default size of normal
5. ✅ Can set size to compact
6. ✅ Retrieves site key from config
7. ✅ Is required by default
8. ✅ Is dehydrated by default

**Features**:
- Tests component configuration
- Tests method chaining
- Tests default values
- Tests config integration

## Architecture Overview

### Component Lifecycle

1. **Initialization**
   - Component class is instantiated via `ReCaptcha::make('field_name')`
   - `setUp()` method configures defaults
   - Validation rule is attached
   - Field is marked as required and dehydrated

2. **Rendering**
   - Blade view is rendered
   - Alpine.js component initializes
   - Polls for Google reCAPTCHA API
   - Renders widget when API is ready

3. **User Interaction**
   - User completes reCAPTCHA challenge
   - Callback stores token in Alpine state
   - Alpine state is synced to Livewire via `$entangle`
   - Livewire state updates on the server

4. **Validation**
   - Form is submitted
   - Laravel validates form data
   - `ReCaptchaRule::passes()` is called
   - Token is sent to Google's API
   - Response is verified
   - Validation result is returned

5. **Error Handling**
   - If validation fails, error message is displayed
   - `resetRecaptcha` event is dispatched
   - Alpine component resets the widget
   - User can try again

### Security Features

1. **Server-Side Validation**
   - All tokens are verified with Google's API
   - Client-side checks are for UX only
   - Cannot be bypassed

2. **User IP Tracking**
   - User's IP is included in verification
   - Provides additional security signals to Google

3. **Comprehensive Logging**
   - All failures are logged
   - Includes error codes and context
   - Enables monitoring and debugging

4. **Token Expiration Handling**
   - Automatic reset on expiration
   - Graceful error handling
   - User-friendly messages

### Integration Points

1. **With Filament Forms**
   - Extends `Field` class
   - Works in all Filament form contexts
   - Supports field wrapper system
   - Compatible with validation

2. **With Livewire**
   - Uses `$entangle` for state binding
   - Respects state binding modifiers
   - Uses `wire:ignore` for DOM isolation
   - Dispatches/listens for events

3. **With Alpine.js**
   - Reactive component for widget management
   - Handles initialization and lifecycle
   - Manages callbacks and errors
   - Provides reset functionality

4. **With Laravel**
   - Standard validation rule
   - Uses HTTP client for API calls
   - Integrates with config system
   - Uses logging facade

## Extension Points

The plugin can be extended in several ways:

1. **Custom Themes**
   - Add custom theme via Closure
   - Supports dynamic theming based on user preferences

2. **Custom Sizes**
   - Supports 'normal' and 'compact'
   - Can be extended for custom sizes

3. **Skip Validation**
   - Can whitelist IPs in development
   - Can disable in testing environments

4. **Custom Error Messages**
   - Override validation messages
   - Customize per-form if needed

## Performance Considerations

1. **Async Loading**
   - reCAPTCHA API loads asynchronously
   - Doesn't block page rendering
   - Uses `defer` attribute

2. **Polling vs Callbacks**
   - Uses polling for initialization
   - More reliable than global callbacks
   - Small performance overhead (100ms intervals)

3. **API Timeout**
   - 10-second timeout prevents hanging
   - Graceful error handling
   - Logs timeout issues

4. **Caching**
   - Config is cached in production
   - HTTP client handles connection pooling
   - Widget state is managed client-side

## Best Practices

1. **Always dispatch resetRecaptcha on validation errors**
2. **Remove recaptcha field from data before saving**
3. **Use HTTP faking in tests**
4. **Monitor logs for unusual patterns**
5. **Combine with rate limiting**
6. **Choose appropriate theme for your design**
7. **Use compact size on mobile forms**

## Future Enhancements

Potential improvements for future versions:

1. Support for reCAPTCHA v3 (invisible)
2. Async Alpine component for on-demand loading
3. Built-in honeypot field option
4. IP whitelisting configuration
5. Custom language support
6. Analytics integration
7. Advanced customization options
