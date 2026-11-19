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
                cleanups: [],

                init() {
                    this.waitForRecaptcha();

                    // Listen for reset events, and for execute events (for manual triggering)
                    this.cleanups.push(
                        Livewire.on('resetRecaptcha', () => {
                            this.reset();
                        }),
                        Livewire.on('executeRecaptcha', () => {
                            this.execute();
                        }),
                    );

                    // Intercept form submission to execute reCAPTCHA
                    this.setupFormInterception();
                },

                // The field left the page, for example hidden by a condition. Its listeners would otherwise stay
                // and act on a widget that is gone, once more for every time the field was shown.
                destroy() {
                    this.cleanups.forEach((cleanup) => cleanup());
                },

                waitForRecaptcha() {
                    // Stop waiting for Google's script once the field has left the page.
                    if (! this.$el.isConnected) {
                        return;
                    }

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

                    const submitter = this.$el.closest('[wire\\:submit]');

                    if (form && submitter) {
                        // Intercept Livewire form submissions
                        const intercept = (e) => {
                            if (!this.state && !this.isExecuting) {
                                e.preventDefault();
                                this.execute();
                            }
                        };

                        submitter.addEventListener('submit', intercept);
                        this.cleanups.push(() => submitter.removeEventListener('submit', intercept));
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
                stopListening: null,

                init() {
                    this.waitForRecaptcha();

                    // Listen for reset events
                    this.stopListening = Livewire.on('resetRecaptcha', () => {
                        this.reset();
                    });
                },

                // The field left the page, for example hidden by a condition. Its listener would otherwise stay
                // and reset a widget that is gone, once more for every time the field was shown.
                destroy() {
                    this.stopListening?.();
                },

                waitForRecaptcha() {
                    // Stop waiting for Google's script once the field has left the page.
                    if (! this.$el.isConnected) {
                        return;
                    }

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
