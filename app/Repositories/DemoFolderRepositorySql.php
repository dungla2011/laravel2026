<?php

namespace App\Repositories;

use App\Models\DemoFolderTbl;

class DemoFolderRepositorySql extends BaseRepositorySql implements DemoFolderRepositoryInterface
{
    public function __construct(DemoFolderTbl $model)
    {
        $this->model = $model;
    }
}
