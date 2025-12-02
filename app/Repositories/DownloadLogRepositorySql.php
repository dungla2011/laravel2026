<?php

namespace App\Repositories;

use App\Models\DownloadLog;

class DownloadLogRepositorySql extends BaseRepositorySql implements DownloadLogRepositoryInterface
{
    public function __construct(DownloadLog $model)
    {
        $this->model = $model;
    }
}
