<?php

namespace App\Providers;

use App\Events\LaravelEventDispatcher;
use App\Repositories\Eloquent\AssuntoRepository;
use App\Repositories\Eloquent\AutorRepository;
use App\Repositories\Eloquent\LivroRepository;
use App\Repositories\Eloquent\RelatorioRepository;
use Core\Domain\Events\EventDispatcherInterface;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Repository\RelatorioRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LivroRepositoryInterface::class,
            LivroRepository::class
        );

        $this->app->bind(
            AutorRepositoryInterface::class,
            AutorRepository::class
        );

        $this->app->bind(
            AssuntoRepositoryInterface::class,
            AssuntoRepository::class
        );

        $this->app->bind(
            EventDispatcherInterface::class,
            LaravelEventDispatcher::class
        );

        $this->app->bind(
            RelatorioRepositoryInterface::class,
            RelatorioRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
