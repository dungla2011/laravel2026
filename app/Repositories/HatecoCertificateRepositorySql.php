<?php

namespace App\Repositories;

use App\Models\HatecoCertificate;

class HatecoCertificateRepositorySql extends BaseRepositorySql implements HatecoCertificateRepositoryInterface
{
    public function __construct(HatecoCertificate $model)
    {
        $this->model = $model;
    }
}
