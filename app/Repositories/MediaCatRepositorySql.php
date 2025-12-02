<?php

namespace App\Repositories;

use App\Models\MediaCat;

class MediaCatRepositorySql extends BaseRepositorySql implements MediaCatRepositoryInterface
{
    public function __construct(MediaCat $model)
    {
        $this->model = $model;
    }
}
