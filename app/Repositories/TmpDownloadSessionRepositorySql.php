<?php

namespace App\Repositories;

use App\Models\TmpDownloadSession;

class TmpDownloadSessionRepositorySql extends BaseRepositorySql implements TmpDownloadSessionRepositoryInterface
{
    public function __construct(TmpDownloadSession $model)
    {
        $this->model = $model;
    }
}
