<?php

namespace App\Repositories;

use App\Models\TransportInfo;

class TransportInfoRepositorySql extends BaseRepositorySql implements TransportInfoRepositoryInterface
{
    public function __construct(TransportInfo $model)
    {
        $this->model = $model;
    }
}
