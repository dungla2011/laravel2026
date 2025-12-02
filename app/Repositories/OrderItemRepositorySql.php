<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepositorySql extends BaseRepositorySql implements OrderItemRepositoryInterface
{
    public function __construct(OrderItem $model)
    {
        $this->model = $model;
    }
}
