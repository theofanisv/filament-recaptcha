<?php

namespace Theograms\FilamentRecaptcha\Forms\Components;

use Closure;
use Theograms\FilamentRecaptcha\Rules\ReCaptchaRule;
use Filament\Forms\Components\Field;

class ReCaptcha extends Field
{
    protected string $view = 'filament-recaptcha::components.recaptcha';

    protected string | Closure | null $theme = 'light';
    protected string | Closure | null $size = 'normal';
    protected string | Closure | null $type = 'checkbox';
    protected string | Closure | null $badge = 'bottomright';
    protected string | Closure | null $formId = null;

    public function theme(string | Closure $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function getTheme(): string
    {
        return $this->evaluate($this->theme);
    }

    public function size(string | Closure $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getSize(): string
    {
        return $this->evaluate($this->size);
    }

    public function type(string | Closure $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type);
    }

    public function badge(string | Closure $badge): static
    {
        $this->badge = $badge;
        return $this;
    }

    public function getBadge(): string
    {
        return $this->evaluate($this->badge);
    }

    public function formId(string | Closure | null $formId): static
    {
        $this->formId = $formId;
        return $this;
    }

    public function getFormId(): ?string
    {
        return $this->evaluate($this->formId);
    }

    public function invisible(): static
    {
        $this->type = 'invisible';
        return $this;
    }

    public function checkbox(): static
    {
        $this->type = 'checkbox';
        return $this;
    }

    public function isInvisible(): bool
    {
        return $this->getType() === 'invisible';
    }

    public function getSiteKey(): string
    {
        return config('filament-recaptcha.site_key');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule(new ReCaptchaRule());
        $this->required();
        $this->label('');
        $this->dehydrated(true);

        $this->validationMessages([
            'required' => __('Please complete the reCAPTCHA verification.'),
        ]);
    }
}
