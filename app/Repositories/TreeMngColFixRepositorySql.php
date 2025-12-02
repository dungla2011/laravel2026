<?php

namespace App\Repositories;

use App\Models\TreeMngColFix;

class TreeMngColFixRepositorySql extends BaseRepositorySql implements TreeMngColFixRepositoryInterface
{
    public function __construct(TreeMngColFix $model)
    {
        $this->model = $model;
    }
}
