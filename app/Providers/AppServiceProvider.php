<?php

namespace App\Providers;

use App\Services\AdminDataService;
use App\Services\PesertaDataService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        $this->app->booted(function () {
            View::composer('peserta.*', function ($view) {
                $user = request()->user();

                if (! $user) {
                    return;
                }

                $context = app(PesertaDataService::class)->forUser($user);
                $data = $view->getData();

                $view->with('pesertaContext', $context);

                foreach ($context as $key => $value) {
                    if (! array_key_exists($key, $data)) {
                        $view->with($key, $value);
                    }
                }
            });

            View::composer('admin.*', function ($view) {
                $context = app(AdminDataService::class)->context();
                $data = $view->getData();

                $view->with('adminContext', $context);

                foreach ($context as $key => $value) {
                    if (! array_key_exists($key, $data)) {
                        $view->with($key, $value);
                    }
                }
            });
        });
    }
}