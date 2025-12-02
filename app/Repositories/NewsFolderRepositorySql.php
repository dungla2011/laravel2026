<?php

namespace App\Repositories;

use App\Models\NewsFolder;

class NewsFolderRepositorySql extends BaseRepositorySql implements NewsFolderRepositoryInterface
{
    public function __construct(NewsFolder $model)
    {
        $this->model = $model;
    }
}
