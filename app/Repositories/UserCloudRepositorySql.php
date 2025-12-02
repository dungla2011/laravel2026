<?php

namespace App\Repositories;

use App\Models\UserCloud;

class UserCloudRepositorySql extends BaseRepositorySql implements UserCloudRepositoryInterface
{
    public function __construct(UserCloud $model)
    {
        $this->model = $model;
    }
}
