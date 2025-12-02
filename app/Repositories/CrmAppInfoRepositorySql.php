<?php

namespace App\Repositories;

use App\Models\CrmAppInfo;

class CrmAppInfoRepositorySql extends BaseRepositorySql implements CrmAppInfoRepositoryInterface
{
    public function __construct(CrmAppInfo $model)
    {
        $this->model = $model;
    }
}
