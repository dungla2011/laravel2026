<?php

namespace App\Repositories;

use App\Models\CartItem;

class CartItemRepositorySql extends BaseRepositorySql implements CartItemRepositoryInterface
{
    public function __construct(CartItem $model)
    {
        $this->model = $model;
    }
}
