<?php

namespace App\Repositories;

use App\Models\MediaFolder;

class MediaFolderRepositorySql extends BaseRepositorySql implements MediaFolderRepositoryInterface
{
    public function __construct(MediaFolder $model)
    {
        $this->model = $model;
    }
}
