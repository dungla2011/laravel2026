<?php

namespace App\Repositories;

use App\Models\BlockUi;

class BlockUiRepositorySql extends BaseRepositorySql implements BlockUiRepositoryInterface
{
    public function __construct(BlockUi $model)
    {
        $this->model = $model;
    }
}
