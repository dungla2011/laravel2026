<?php

namespace App\Repositories;

use App\Models\HrSampleTimeEvent;

class HrSampleTimeEventRepositorySql extends BaseRepositorySql implements HrSampleTimeEventRepositoryInterface
{
    public function __construct(HrSampleTimeEvent $model)
    {
        $this->model = $model;
    }
}
