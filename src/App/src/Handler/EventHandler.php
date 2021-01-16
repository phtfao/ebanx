<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use PhpParser\Node\Stmt\TryCatch;

class EventHandler implements RequestHandlerInterface
{
    const BALANCE_ADDR = __DIR__ . "/../../../../data/";
    const DEPOSIT = 'DEPOSIT';
    const WITHDRAW = 'WITHDRAW';
    const TRANSFER = 'TRANSFER';

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $data = $request->getParsedBody();

            switch(strtoupper($data['type'])) {
                case self::DEPOSIT:
                    $responseDeposit = $this->deposit((int)$data['destination'], $data['amount']);
                    return new JsonResponse($responseDeposit, 201);
                    break;

                case self::WITHDRAW:
                    if (! $this->checkAccountExists((int)$data['origin'])) {
                        return new HtmlResponse('0', 404);
                    }
                    
                    $responseWithdraw = $this->withdraw((int)$data['origin'], $data['amount']);
                    return new JsonResponse($responseWithdraw, 201);
                    break;

                case self::TRANSFER:
                    if (! $this->checkAccountExists((int)$data['origin'])) {
                        return new HtmlResponse('0', 404);
                    }

                    $responseTransfer = $this->transfer((int)$data['origin'], (int)$data['destination'], $data['amount']);
                    return new JsonResponse($responseTransfer, 201);
                    break;

                default:
                    return new HtmlResponse('0', 404);
            }
        } catch (\Throwable $th) {
            //return new HtmlResponse('0', 404);
            return new HtmlResponse($th->getMessage() . " on " . $th->getFile() . " line " . $th->getLine() . " \n " . $th->getTraceAsString(), 500);
        }
    }

    private function checkAccountExists(int $accountId)
    {
        $accountBalanceAddr = self::BALANCE_ADDR . "{$accountId}.balance";

        if (! is_file($accountBalanceAddr) ) {
            return false;
        }
        else {
            return true;
        }
    }

    private function getBalance (int $accountId)
    {
        if ($this->checkAccountExists($accountId)) {
            $accountBalanceAddr = self::BALANCE_ADDR . "{$accountId}.balance";
            $balance = file_get_contents($accountBalanceAddr);
            return $balance;
        } else {
            return 0;
        }
    }

    private function setBalance (int $accountId, $balance)
    {
        $accountBalanceAddr = self::BALANCE_ADDR . "{$accountId}.balance";

        $balance = file_put_contents($accountBalanceAddr, $balance);
        return true;
    }

    private function deposit(int $destinationId, $amount) : Array
    {
        $oldBalance = $this->getBalance($destinationId);
        $newBalance = $oldBalance + $amount;
        $this->setBalance($destinationId, $newBalance);

        $response = [
            "destination" => [
                "id" => (string)$destinationId,
                "balance" => $newBalance,
            ]
        ];

        return $response;
    }

    private function withdraw($originId, $amount) : Array
    {
        $oldBalance = $this->getBalance($originId);
        $newBalance = $oldBalance - $amount;
        $this->setBalance($originId, $newBalance);

        $response = [
            "origin" => [
                "id" => (string)$originId,
                "balance" => $newBalance,
            ]
        ];

        return $response;
    }


    private function transfer($originId, $destinationId, $amount) : Array
    {
        $responseWithdraw = $this->withdraw($originId, $amount);
        $responseDeposit = $this->deposit($destinationId, $amount);

        $response = array_merge(
            $responseWithdraw,
            $responseDeposit
        );

        return $response;
    }
}
