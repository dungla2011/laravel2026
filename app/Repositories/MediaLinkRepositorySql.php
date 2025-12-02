<?php

namespace App\Repositories;

use App\Models\MediaLink;

class MediaLinkRepositorySql extends BaseRepositorySql implements MediaLinkRepositoryInterface
{
    public function __construct(MediaLink $model)
    {
        $this->model = $model;
    }
}
