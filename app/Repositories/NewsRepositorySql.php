<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepositorySql extends BaseRepositorySql implements NewsRepositoryInterface
{
    public function __construct(News $model)
    {
        $this->model = $model;
    }
}
