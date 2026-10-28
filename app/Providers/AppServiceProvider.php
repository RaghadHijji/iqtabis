<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // only admins can open the dashboard (FR-25)
        Gate::define('admin', fn (User $user) => $user->is_admin);

        // the same day in the Hijri (Umm al-Qura) calendar, e.g. "13 جمادى الأولى 1448 هـ"
        Carbon::macro('hijri', function (bool $withDay = false): string {
            /** @var CarbonInterface $this */
            if (! class_exists(\IntlDateFormatter::class)) {
                return '';
            }

            $arabic = app()->getLocale() === 'ar';
            $formatter = new \IntlDateFormatter(
                ($arabic ? 'ar-SA' : 'en').'@calendar=islamic-umalqura;numbers=latn',
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::NONE,
                $this->getTimezone(),
                \IntlDateFormatter::TRADITIONAL,
                ($withDay ? 'EEEE ' : '').($arabic ? 'd MMMM y' : 'MMMM d, y'),
            );

            return $formatter->format($this).($arabic ? ' هـ' : ' AH');
        });
    }
}
