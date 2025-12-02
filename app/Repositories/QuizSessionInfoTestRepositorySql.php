<?php

namespace App\Repositories;

use App\Models\QuizSessionInfoTest;

class QuizSessionInfoTestRepositorySql extends BaseRepositorySql implements QuizSessionInfoTestRepositoryInterface
{
    public function __construct(QuizSessionInfoTest $model)
    {
        $this->model = $model;
    }
}
