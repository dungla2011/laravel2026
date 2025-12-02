<?php

namespace App\Repositories;

use App\Models\OcrImage;

class OcrImageRepositorySql extends BaseRepositorySql implements OcrImageRepositoryInterface
{
    public function __construct(OcrImage $model)
    {
        $this->model = $model;
    }
}
