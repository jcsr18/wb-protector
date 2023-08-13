<?php

declare(strict_types=1);

namespace App\Controller;

use App\Receiver\AsaasReceiver;
use Exception;
use Hyperf\HttpServer\Contract\RequestInterface as Request;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

class AsaasController extends AbstractController
{
    public function store(string $gatewayAccount, Request $request, Response $response): ResponseInterface
    {
        try {
            return (new AsaasReceiver())->response(
                \Hyperf\Config\config('receivers.projectone_asaas') . "/{$gatewayAccount}",
                $request->all(),
                $request->getHeaders()
            );
        } catch (Exception $exception) {
            return $response->json(['error' => $exception->getMessage()])->withStatus(HttpStatus::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
