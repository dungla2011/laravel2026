<?php

namespace App\Repositories;

use App\Models\FileRefer;

class FileReferRepositorySql extends BaseRepositorySql implements FileReferRepositoryInterface
{
    public function __construct(FileRefer $model)
    {
        $this->model = $model;
    }
}
