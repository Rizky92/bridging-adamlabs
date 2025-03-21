<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Stringable::macro('value', fn (): string => /** @psalm-scope-this Illuminate\Support\Stringable */ $this->value);
        Stringable::macro('toInt', fn (): int => /** @psalm-scope-this Illuminate\Support\Stringable */ intval($this->value));
        Stringable::macro('wrap', fn (string $startsWith, ?string $endsWith = null): Stringable => /** @psalm-scope-this Illuminate\Support\Stringable */ is_null($endsWith)
            ? new Stringable($startsWith.$this->value.$startsWith)
            : new Stringable($startsWith.$this->value.$endsWith));
    }
}
