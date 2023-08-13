<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Mongo;
use MongoDB\InsertOneResult;

abstract class AbstractRepository
{
    public Mongo $mongo;

    public function __construct()
    {
        $this->mongo = new Mongo();
    }
}
