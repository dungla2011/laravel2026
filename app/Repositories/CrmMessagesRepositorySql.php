<?php

namespace App\Repositories;

use App\Models\CrmMessages;

class CrmMessagesRepositorySql extends BaseRepositorySql implements CrmMessagesRepositoryInterface
{
    public function __construct(CrmMessages $model)
    {
        $this->model = $model;
    }
}
