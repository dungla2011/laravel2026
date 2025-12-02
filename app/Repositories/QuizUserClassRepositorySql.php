<?php

namespace App\Repositories;

use App\Models\QuizUserClass;

class QuizUserClassRepositorySql extends BaseRepositorySql implements QuizUserClassRepositoryInterface
{
    public function __construct(QuizUserClass $model)
    {
        $this->model = $model;
    }
}
