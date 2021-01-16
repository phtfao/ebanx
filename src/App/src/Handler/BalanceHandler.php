<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class BalanceHandler implements RequestHandlerInterface
{
    const BALANCE_ADDR = __DIR__ . "/../../../../data/";

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $accountId = $request->getQueryParams()['account_id'] ?? '';
        $accountId = (int)htmlspecialchars($accountId, ENT_HTML5, 'UTF-8');        

        if (is_nan($accountId)) {
            return new HtmlResponse('0', 404);
            
        }

        $accountBalanceAddr = self::BALANCE_ADDR . "{$accountId}.balance";

        if (! is_file($accountBalanceAddr) ) {
            return new HtmlResponse('0', 404);
        }
        $balance = file_get_contents($accountBalanceAddr);
        return new HtmlResponse($balance, 200);
    }
}
