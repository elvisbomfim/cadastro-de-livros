<?php

namespace App\Providers;

use App\Repositories\Eloquent\AssuntoRepository;
use App\Repositories\Eloquent\AutorRepository;
use App\Repositories\Eloquent\LivroRepository;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Repository\LivroRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
