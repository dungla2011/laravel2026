<?php

namespace App\Repositories;

use App\Models\DonViHanhChinh;

class DonViHanhChinhRepositorySql extends BaseRepositorySql implements DonViHanhChinhRepositoryInterface
{
    public function __construct(DonViHanhChinh $model)
    {
        $this->model = $model;
    }
}
