<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try {
            // Disable sql_require_primary_key for Aiven MySQL compatibility
            DB::statement('SET SESSION sql_require_primary_key = 0');
            // Attempt to retrieve the timezone from the settings table
            $timezoneSetting = Setting::where('key', 'timezone')->first();
            
            if ($timezoneSetting && $timezoneSetting->value) {
                // If the timezone setting exists, set the timezone dynamically
                config(['app.timezone' => $timezoneSetting->value]);
                date_default_timezone_set($timezoneSetting->value);
            } else {
                // If the timezone setting doesn't exist or is empty, fallback to a default timezone
                config(['app.timezone' => 'Asia/Kolkata']);
                date_default_timezone_set('Asia/Kolkata');
            }

            // Attempt to retrieve mail settings from the settings table
            $mailSettings = Setting::whereIn('key', [
                'mailer', 'host', 'port', 'encryption', 'username', 'password', 'from_address', 'from_name'
            ])->pluck('value', 'key');

            // Set mail configuration
            Config::set('mail.default', $mailSettings['mailer'] ?? 'smtp');
            Config::set('mail.mailers.smtp.transport', $mailSettings['mailer'] ?? 'smtp');
            Config::set('mail.mailers.smtp.host', $mailSettings['host'] ?? 'smtp.example.com');
            Config::set('mail.mailers.smtp.port', $mailSettings['port'] ?? 587);
            Config::set('mail.mailers.smtp.encryption', $mailSettings['encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.username', $mailSettings['username'] ?? null);
            Config::set('mail.mailers.smtp.password', $mailSettings['password'] ?? null);
            Config::set('mail.from.address', $mailSettings['from_address'] ?? 'noreply@example.com');
            Config::set('mail.from.name', $mailSettings['from_name'] ?? 'Example');

        } catch (Exception $e) {
            // If there is an exception (e.g., database connection fails), log the error
            Log::error('Database connection failed: ' . $e->getMessage());

            // Fallback to default timezone
            config(['app.timezone' => 'Asia/Kolkata']);
            date_default_timezone_set('Asia/Kolkata');

            // Fallback to default mail settings
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', 'smtp.example.com');
            Config::set('mail.mailers.smtp.port', 587);
            Config::set('mail.mailers.smtp.encryption', 'tls');
            Config::set('mail.mailers.smtp.username', null);
            Config::set('mail.mailers.smtp.password', null);
            Config::set('mail.from.address', 'noreply@example.com');
            Config::set('mail.from.name', 'Example');
        }
    }
}
