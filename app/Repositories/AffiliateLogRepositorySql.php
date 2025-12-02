<?php

namespace App\Repositories;

use App\Models\AffiliateLog;

class AffiliateLogRepositorySql extends BaseRepositorySql implements AffiliateLogRepositoryInterface
{
    public function __construct(AffiliateLog $model)
    {
        $this->model = $model;
    }
}
