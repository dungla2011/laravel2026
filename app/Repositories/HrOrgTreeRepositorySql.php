<?php

namespace App\Repositories;

use App\Models\HrOrgTree;

class HrOrgTreeRepositorySql extends BaseRepositorySql implements HrOrgTreeRepositoryInterface
{
    public function __construct(HrOrgTree $model)
    {
        $this->model = $model;
    }
}
