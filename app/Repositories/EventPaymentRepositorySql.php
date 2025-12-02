<?php

namespace App\Repositories;

use App\Models\EventPayment;

class EventPaymentRepositorySql extends BaseRepositorySql implements EventPaymentRepositoryInterface
{
    public function __construct(EventPayment $model)
    {
        $this->model = $model;
    }
}
