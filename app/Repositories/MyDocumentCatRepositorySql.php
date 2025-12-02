<?php

namespace App\Repositories;

use App\Models\MyDocumentCat;

class MyDocumentCatRepositorySql extends BaseRepositorySql implements MyDocumentCatRepositoryInterface
{
    public function __construct(MyDocumentCat $model)
    {
        $this->model = $model;
    }
}
