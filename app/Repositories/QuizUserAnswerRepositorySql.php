<?php

namespace App\Repositories;

use App\Models\QuizUserAnswer;

class QuizUserAnswerRepositorySql extends BaseRepositorySql implements QuizUserAnswerRepositoryInterface
{
    public function __construct(QuizUserAnswer $model)
    {
        $this->model = $model;
    }
}
