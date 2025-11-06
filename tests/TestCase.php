<?php

namespace Tests;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Registrar provider após RefreshDatabase inicializar o banco
        if (!$this->app->bound(\Core\Domain\Repository\AssuntoRepositoryInterface::class)) {
            $this->app->register(AppServiceProvider::class);
        }
    }
}