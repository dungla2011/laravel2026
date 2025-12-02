<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepositorySql extends BaseRepositorySql implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }
}
