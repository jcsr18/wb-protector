<?php

declare(strict_types=1);

namespace App\Receiver;

use App\Contract\ReceiverContract;
use App\Repository\SenderRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Hyperf\HttpServer\Response;
use MongoDB\InsertOneResult;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

abstract class AbstractReceiver implements ReceiverContract
{
    public SenderRepository $senderRepository;

    public string $provider = 'sample_receiver_name';

    public function __construct()
    {
        $this->senderRepository = new SenderRepository();
    }

    public function response(string $url, array $requestData, array $headers): ResponseInterface
    {
        $payload = [
            'url' => $url,
            'request' => $requestData,
            'headers' => $headers,
            'attempts' => 1,
            'provider' => $this->provider,
            'sent_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        [
            'response' => $payload['response'],
            'status' => $payload['status'],
        ] = $this->request($url, $requestData, $headers);

        $this->createInCollection($payload);

        return (new Response())->json([])->withStatus(HttpStatus::HTTP_OK);
    }

    public function request(string $url, array $requestData, array $headers): array
    {
        try {
            $response = (new Client())->request('POST', $url, [
                'json' => $requestData,
                'headers' => $headers,
            ]);

            return [
                'response' => json_decode($response->getBody()->getContents()),
                'status' => true,
            ];
        } catch (ClientException $exception) {
            return [
                'response' => json_decode($exception->getResponse()->getBody()->getContents()),
                'status' => false,
            ];
        }
    }

    protected function createInCollection(array $payload): InsertOneResult
    {
        return $this->senderRepository->create($payload);
    }
}
