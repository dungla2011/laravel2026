<?php

namespace App\Repositories;

use App\Models\OrderInfo;

class OrderInfoRepositorySql extends BaseRepositorySql implements OrderInfoRepositoryInterface
{
    public function __construct(OrderInfo $model)
    {
        $this->model = $model;
    }
}
