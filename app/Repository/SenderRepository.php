<?php

declare(strict_types=1);

namespace App\Repository;

use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;

class SenderRepository extends AbstractRepository
{
    public const COLLECTION = 'senders';

    public function create(array $payload): InsertOneResult
    {
        return $this->mongo->insert(self::COLLECTION, $payload);
    }

    public function getFailed(): iterable
    {
        return $this->mongo->find(self::COLLECTION, ['status' => false]);
    }

    public function update(string|int $id, array $payload, string $findBy = '_id'): UpdateResult
    {
        return $this->mongo->update(self::COLLECTION, $findBy, $id, $payload);
    }
}
