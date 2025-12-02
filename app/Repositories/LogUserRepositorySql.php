<?php

namespace App\Repositories;

use App\Models\LogUser;

class LogUserRepositorySql extends BaseRepositorySql implements LogUserRepositoryInterface
{
    public function __construct(LogUser $model)
    {
        $this->model = $model;
    }
}
