<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositorySql extends BaseRepositorySql implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
