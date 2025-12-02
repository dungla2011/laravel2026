<?php

namespace App\Repositories;

use App\Models\MediaVendor;

class MediaVendorRepositorySql extends BaseRepositorySql implements MediaVendorRepositoryInterface
{
    public function __construct(MediaVendor $model)
    {
        $this->model = $model;
    }
}
