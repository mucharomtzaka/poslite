<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;


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
        }

        config(['database.connections.sqlite.database' => $dbPath]);
       
    }
}
