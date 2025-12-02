<?php

namespace App\Repositories;

use App\Models\CrmMessage;

class CrmMessageRepositorySql extends BaseRepositorySql implements CrmMessageRepositoryInterface
{
    public function __construct(CrmMessage $model)
    {
        $this->model = $model;
    }
}
