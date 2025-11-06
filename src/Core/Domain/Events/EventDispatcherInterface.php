<?php

namespace Core\Domain\Events;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;
}

