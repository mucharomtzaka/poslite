<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;


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
        //
        Schema::defaultStringLength(191);

        $dbPath = '/tmp/database.sqlite';

        if (!file_exists($dbPath)) {
            File::put($dbPath, '');
            config(['database.connections.sqlite.database' => $dbPath]);
             // Run migrations to create necessary tables
            Artisan::call('migrate', ['--force' => true]);
        }
       
    }
}
