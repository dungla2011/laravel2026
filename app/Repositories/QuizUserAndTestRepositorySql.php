<?php

namespace App\Repositories;

use App\Models\QuizUserAndTest;

class QuizUserAndTestRepositorySql extends BaseRepositorySql implements QuizUserAndTestRepositoryInterface
{
    public function __construct(QuizUserAndTest $model)
    {
        $this->model = $model;
    }
}
