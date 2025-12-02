<?php

namespace App\Repositories;

use App\Models\CrmMessageGroup;

class CrmMessageGroupRepositorySql extends BaseRepositorySql implements CrmMessageGroupRepositoryInterface
{
    public function __construct(CrmMessageGroup $model)
    {
        $this->model = $model;
    }
}
