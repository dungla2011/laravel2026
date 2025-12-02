<?php

namespace App\Repositories;

use App\Models\FileUpload;

class FileRepositorySql extends BaseRepositorySql implements FileRepositoryInterface
{
    public function __construct(FileUpload $model)
    {
        $this->model = $model;
    }
}
