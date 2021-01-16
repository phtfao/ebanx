<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class EventHandlerFactory
{
    public function __invoke(ContainerInterface $container) : EventHandler
    {
        return new EventHandler();
    }
}
