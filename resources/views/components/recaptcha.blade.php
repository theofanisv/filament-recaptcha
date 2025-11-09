<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if($isInvisible())
        <div
            x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('google-recaptcha', 'theofanisv/filament-recaptcha'))]"
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                widgetId: null,
                siteKey: @js($getSiteKey()),
                badge: @js($getBadge()),
                formId: @js($getFormId()),
                isExecuting: false,

                init() {
                    this.waitForRecaptcha();

                    // Listen for reset events
                    Livewire.on('resetRecaptcha', () => {
                        this.reset();
                    });

                    // Listen for execute events (for manual triggering)
                    Livewire.on('executeRecaptcha', () => {
                        this.execute();
                    });

                    // Intercept form submission to execute reCAPTCHA
                    this.setupFormInterception();
                },

                waitForRecaptcha() {
                    if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
                        this.renderWidget();
                    } else {
                        setTimeout(() => this.waitForRecaptcha(), 100);
                    }
                },

                renderWidget() {
                    try {
                        this.widgetId = grecaptcha.render(this.$refs.recaptcha, {
                            'sitekey': this.siteKey,
                            'size': 'invisible',
                            'badge': this.badge,
                            'callback': (response) => {
                                this.state = response;
                                this.isExecuting = false;
                                // Auto-submit form after successful verification
                                if (this.formId) {
                                    const form = document.getElementById(this.formId);
                                    if (form) {
                                        form.submit();
                                    }
                                }
                            },
                            'expired-callback': () => {
                                this.reset();
                                this.isExecuting = false;
                            },
                            'error-callback': () => {
                                this.reset();
                                this.isExecuting = false;
                            }
                        });
                    } catch (error) {
                        console.error('reCAPTCHA render error:', error);
                        this.isExecuting = false;
                    }
                },

                setupFormInterception() {
                    // Find the form element
                    let form = this.$el.closest('form');

                    if (!form && this.formId) {
                        form = document.getElementById(this.formId);
                    }

                    if (form) {
                        // Intercept Livewire form submissions
                        this.$el.closest('[wire\\:submit]')?.addEventListener('submit', (e) => {
                            if (!this.state && !this.isExecuting) {
                                e.preventDefault();
                                this.execute();
                            }
                        });
                    }
                },

                execute() {
                    if (this.widgetId !== null && !this.isExecuting) {
                        try {
                            this.isExecuting = true;
                            grecaptcha.execute(this.widgetId);
                        } catch (error) {
                            console.error('reCAPTCHA execute error:', error);
                            this.isExecuting = false;
                        }
                    }
                },

                reset() {
                    this.state = null;
                    this.isExecuting = false;
                    if (this.widgetId !== null) {
                        try {
                            grecaptcha.reset(this.widgetId);
                        } catch (error) {
                            console.error('reCAPTCHA reset error:', error);
                        }
                    }
                }
            }"
            wire:ignore
        >
            <div x-ref="recaptcha"></div>
        </div>
    @else
        <div
            x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('google-recaptcha', 'theofanisv/filament-recaptcha'))]"
            x-data="{
                state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                widgetId: null,
                siteKey: @js($getSiteKey()),
                theme: @js($getTheme()),
                size: @js($getSize()),

                init() {
                    this.waitForRecaptcha();

                    // Listen for reset events
                    Livewire.on('resetRecaptcha', () => {
                        this.reset();
                    });
                },

                waitForRecaptcha() {
                    if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
                        this.renderWidget();
                    } else {
                        setTimeout(() => this.waitForRecaptcha(), 100);
                    }
                },

                renderWidget() {
                    try {
                        this.widgetId = grecaptcha.render(this.$refs.recaptcha, {
                            'sitekey': this.siteKey,
                            'theme': this.theme,
                            'size': this.size,
                            'callback': (response) => {
                                this.state = response;
                            },
                            'expired-callback': () => {
                                this.reset();
                            },
                            'error-callback': () => {
                                this.reset();
                            }
                        });
                    } catch (error) {
                        console.error('reCAPTCHA render error:', error);
                    }
                },

                reset() {
                    this.state = null;
                    if (this.widgetId !== null) {
                        try {
                            grecaptcha.reset(this.widgetId);
                        } catch (error) {
                            console.error('reCAPTCHA reset error:', error);
                        }
                    }
                }
            }"
            wire:ignore
        >
            <div x-ref="recaptcha"></div>
        </div>
    @endif
</x-dynamic-component>
