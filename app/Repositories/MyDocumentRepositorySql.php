<?php

namespace App\Repositories;

use App\Models\MyDocument;

class MyDocumentRepositorySql extends BaseRepositorySql implements MyDocumentRepositoryInterface
{
    public function __construct(MyDocument $model)
    {
        $this->model = $model;
    }
}
