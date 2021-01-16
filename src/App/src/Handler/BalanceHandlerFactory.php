<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class BalanceHandlerFactory
{
    public function __invoke(ContainerInterface $container) : BalanceHandler
    {
        return new BalanceHandler();
    }
}
