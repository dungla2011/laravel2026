<?php

namespace App\Repositories;

use App\Models\QuizClass;

class QuizClassRepositorySql extends BaseRepositorySql implements QuizClassRepositoryInterface
{
    public function __construct(QuizClass $model)
    {
        $this->model = $model;
    }
}
