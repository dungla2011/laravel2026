<?php

namespace App\Repositories;

use App\Models\CloudServer;

class CloudServerRepositorySql extends BaseRepositorySql implements CloudServerRepositoryInterface
{
    public function __construct(CloudServer $model)
    {
        $this->model = $model;
    }
}
