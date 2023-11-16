<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CadastrarMovimentacaoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CadastrarMovimentacaoService::class, function ($app) {
            return new CadastrarMovimentacaoService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
