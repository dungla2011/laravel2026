<?php

namespace App\Repositories;

use App\Models\TestMongo1;

class TestMongo1RepositorySql extends BaseRepositorySql implements TestMongo1RepositoryInterface
{
    public function __construct(TestMongo1 $model)
    {
        $this->model = $model;
    }
}
