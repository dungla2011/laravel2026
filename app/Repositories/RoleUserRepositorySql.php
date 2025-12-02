<?php

namespace App\Repositories;

use App\Models\RoleUser;

class RoleUserRepositorySql extends BaseRepositorySql implements RoleUserRepositoryInterface
{
    public function __construct(RoleUser $model)
    {
        $this->model = $model;
    }
}
