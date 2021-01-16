<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class ResetHandler implements RequestHandlerInterface
{
    const BALANCE_ADDR = __DIR__ . "/../../../../data/";

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        array_map('unlink', glob(self::BALANCE_ADDR . "*.balance"));
        return new HtmlResponse("OK");
    }
}
