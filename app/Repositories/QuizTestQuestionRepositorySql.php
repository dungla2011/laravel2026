<?php

namespace App\Repositories;

use App\Models\QuizTestQuestion;

class QuizTestQuestionRepositorySql extends BaseRepositorySql implements QuizTestQuestionRepositoryInterface
{
    public function __construct(QuizTestQuestion $model)
    {
        $this->model = $model;
    }
}
