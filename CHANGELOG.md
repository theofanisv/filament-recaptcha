# Changelog

All notable changes to the Filament reCAPTCHA plugin will be documented in this file.

## [1.1.0] - 2025-01-09

### Added
- **Invisible reCAPTCHA support** - Seamless bot protection without user interaction
- `invisible()` method to enable invisible mode
- `badge()` method to configure badge position (bottomright, bottomleft, inline)
- `formId()` method for specifying form ID with invisible mode
- `isInvisible()` helper method to check current mode
- `checkbox()` method to explicitly set checkbox mode
- `type()` and `getType()` methods for managing reCAPTCHA type
- Automatic form interception for invisible mode
- Manual execution capability via `executeRecaptcha` event
- Configuration options for default type and badge position
- Comprehensive tests for invisible mode (11 new test cases)
- Dedicated invisible reCAPTCHA guide (`INVISIBLE_RECAPTCHA_GUIDE.md`) with 5 complete examples
- Updated README with invisible mode documentation

### Changed
- Blade view now conditionally renders checkbox or invisible widget based on type
- Enhanced Alpine.js component with mode-specific logic and form interception
- Configuration file expanded to support both checkbox and invisible modes
- Documentation updated throughout to reflect both modes

### Improved
- Better form submission handling for invisible mode with automatic execution
- Enhanced error handling for both checkbox and invisible modes
- Improved badge positioning control for better UI flexibility
- More flexible component configuration with fluent API
- Comprehensive examples for public-facing forms (ticket payment, registration, etc.)

## [1.0.0] - 2025-01-09

### Added
- Initial release
- Google reCAPTCHA v2 checkbox integration
- Full Filament v4 compatibility
- Server-side validation with ReCaptchaRule
- Alpine.js reactive integration
- Automatic token refresh on validation errors
- Support for light/dark themes (checkbox mode)
- Support for normal/compact sizes (checkbox mode)
- Comprehensive error handling and logging
- Unit and feature tests (9 test cases)
- Complete documentation (README, INSTALLATION, USAGE_EXAMPLES, PLUGIN_STRUCTURE)

### Security
- Bulletproof server-side validation
- User IP tracking for additional security
- Comprehensive error logging for monitoring
- Automatic token expiration handling
