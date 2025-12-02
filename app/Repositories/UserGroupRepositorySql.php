<?php

namespace App\Repositories;

use App\Models\UserGroup;

class UserGroupRepositorySql extends BaseRepositorySql implements UserGroupRepositoryInterface
{
    public function __construct(UserGroup $model)
    {
        $this->model = $model;
    }
}
