<?php

namespace App\Repositories;

use App\Models\MenuTree;

class MenuTreeRepositorySql extends BaseRepositorySql implements MenuTreeRepositoryInterface
{
    public function __construct(MenuTree $model)
    {
        $this->model = $model;
    }
}
