<?php

namespace App\Repositories;

use App\Models\ConferenceInfo;

class ConferenceInfoRepositorySql extends BaseRepositorySql implements ConferenceInfoRepositoryInterface
{
    public function __construct(ConferenceInfo $model)
    {
        $this->model = $model;
    }
}
