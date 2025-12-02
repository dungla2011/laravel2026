<?php

namespace App\Repositories;

use App\Models\EventUserPayment;

class EventUserPaymentRepositorySql extends BaseRepositorySql implements EventUserPaymentRepositoryInterface
{
    public function __construct(EventUserPayment $model)
    {
        $this->model = $model;
    }
}
