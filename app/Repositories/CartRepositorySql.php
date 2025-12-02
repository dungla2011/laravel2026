<?php

namespace App\Repositories;

use App\Models\Cart;

class CartRepositorySql extends BaseRepositorySql implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        $this->model = $model;
    }
}
