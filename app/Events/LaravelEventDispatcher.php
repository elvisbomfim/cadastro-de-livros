<?php

namespace App\Events;

use Core\Domain\Events\EventDispatcherInterface;
use Core\Domain\Events\EventInterface;
use Illuminate\Contracts\Events\Dispatcher;

class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private Dispatcher $dispatcher
    ) {}

    public function dispatch(EventInterface $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}

