<?php

namespace App\Contract;

use Psr\Http\Message\ResponseInterface;

interface ReceiverContract
{
    public function response(string $url, array $requestData, array $headers): ResponseInterface;
}