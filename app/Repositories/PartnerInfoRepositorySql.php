<?php

namespace App\Repositories;

use App\Models\PartnerInfo;

class PartnerInfoRepositorySql extends BaseRepositorySql implements PartnerInfoRepositoryInterface
{
    public function __construct(PartnerInfo $model)
    {
        $this->model = $model;
    }
}
