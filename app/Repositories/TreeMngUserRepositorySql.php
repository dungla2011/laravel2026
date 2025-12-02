<?php

namespace App\Repositories;

use App\Models\GiaPhaUser;

class TreeMngUserRepositorySql extends BaseRepositorySql implements TreeMngUserRepositoryInterface
{
    public function __construct(GiaPhaUser $model)
    {
        $this->model = $model;
    }
}
