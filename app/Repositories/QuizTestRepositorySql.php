<?php

namespace App\Repositories;

use App\Models\QuizTest;

class QuizTestRepositorySql extends BaseRepositorySql implements QuizTestRepositoryInterface
{
    public function __construct(QuizTest $model)
    {
        $this->model = $model;
    }
}
