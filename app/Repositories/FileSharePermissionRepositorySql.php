<?php

namespace App\Repositories;

use App\Models\FileSharePermission;

class FileSharePermissionRepositorySql extends BaseRepositorySql implements FileSharePermissionRepositoryInterface
{
    public function __construct(FileSharePermission $model)
    {
        $this->model = $model;
    }
}
