<?php

namespace App\Repositories;

use App\Models\MyTreeInfo;

class MyTreeInfoRepositorySql extends BaseRepositorySql implements MyTreeInfoRepositoryInterface
{
    public function __construct(MyTreeInfo $model)
    {
        $this->model = $model;
    }
}
