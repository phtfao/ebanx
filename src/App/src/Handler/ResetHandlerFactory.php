<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class ResetHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ResetHandler
    {
        return new ResetHandler();
    }
}
