<?php

namespace App\Repositories;

use App\Models\NetworkMarketing;

class NetworkMarketingRepositorySql extends BaseRepositorySql implements NetworkMarketingRepositoryInterface
{
    public function __construct(NetworkMarketing $model)
    {
        $this->model = $model;
    }
}
