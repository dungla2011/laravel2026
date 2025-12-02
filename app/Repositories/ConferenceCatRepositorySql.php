<?php

namespace App\Repositories;

use App\Models\ConferenceCat;

class ConferenceCatRepositorySql extends BaseRepositorySql implements ConferenceCatRepositoryInterface
{
    public function __construct(ConferenceCat $model)
    {
        $this->model = $model;
    }
}
