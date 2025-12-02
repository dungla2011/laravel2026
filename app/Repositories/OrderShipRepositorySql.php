<?php

namespace App\Repositories;

use App\Models\OrderShip;

class OrderShipRepositorySql extends BaseRepositorySql implements OrderShipRepositoryInterface
{
    public function __construct(OrderShip $model)
    {
        $this->model = $model;
    }
}
