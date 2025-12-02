<?php

namespace App\Repositories;

use App\Models\FolderFile;

class FolderFileRepositorySql extends BaseRepositorySql implements FolderFileRepositoryInterface
{
    public function __construct(FolderFile $model)
    {
        $this->model = $model;
    }
}
