<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\TagDemo;

class TagRepositorySql extends BaseRepositorySql implements TagRepositoryInterface
{
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }
}
