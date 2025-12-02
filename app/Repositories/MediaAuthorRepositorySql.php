<?php

namespace App\Repositories;

use App\Models\MediaAuthor;

class MediaAuthorRepositorySql extends BaseRepositorySql implements MediaAuthorRepositoryInterface
{
    public function __construct(MediaAuthor $model)
    {
        $this->model = $model;
    }
}
