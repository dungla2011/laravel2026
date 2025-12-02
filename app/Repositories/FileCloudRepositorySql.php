<?php

namespace App\Repositories;

use App\Models\FileCloud;

class FileCloudRepositorySql extends BaseRepositorySql implements FileCloudRepositoryInterface
{
    public function __construct(FileCloud $model)
    {
        $this->model = $model;
    }
}
