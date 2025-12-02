<?php

namespace App\Repositories;

use App\Models\MediaItem;

class MediaItemRepositorySql extends BaseRepositorySql implements MediaItemRepositoryInterface
{
    public function __construct(MediaItem $model)
    {
        $this->model = $model;
    }
}
