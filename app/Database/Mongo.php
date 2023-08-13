<?php

declare(strict_types=1);

namespace App\Database;

use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;

use function Hyperf\Config\config;

class Mongo
{
    private Database $mongo;

    public function __construct()
    {
        $this->mongo = $this->createConnection();
    }

    private function createConnection(): Database
    {
        $dsn = config('databases.mongo.dsn');
        $database = config('databases.mongo.database');

        $client = new Client($dsn ?? $this->generateUrlConnection($database), [], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
            ],
        ]);
        $client->selectDatabase($database);

        return $client->{$database};
    }

    private function generateUrlConnection(string $database): string
    {
        $host = config('databases.mongo.host');
        $username = config('databases.mongo.username');
        $password = config('databases.mongo.password');
        $port = config('databases.mongo.port');

        return "mongodb://{$username}:{$password}@{$host}:{$port}/{$database}?authSource=admin";
    }

    public function insert(string $collection, array $payload): InsertOneResult
    {
        return $this->mongo->{$collection}->insertOne($payload);
    }

    public function find(string $collection, array $filter): iterable
    {
        return $this->mongo->{$collection}->find($filter);
    }

    public function update(string $collection, string $findBy, string|int $id, array $payload): UpdateResult
    {
        return $this->mongo->{$collection}->updateOne([$findBy => new ObjectId($id)], [
            '$set' => $payload,
        ]);
    }
}
