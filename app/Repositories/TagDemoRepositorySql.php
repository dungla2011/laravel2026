<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\TagDemo;

class TagDemoRepositorySql extends BaseRepositorySql implements TagDemoRepositoryInterface
{
    public function __construct(TagDemo $model)
    {
        $this->model = $model;
    }
}
