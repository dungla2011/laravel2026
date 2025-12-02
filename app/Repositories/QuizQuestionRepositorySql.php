<?php

namespace App\Repositories;

use App\Models\QuizQuestion;

class QuizQuestionRepositorySql extends BaseRepositorySql implements QuizQuestionRepositoryInterface
{
    public function __construct(QuizQuestion $model)
    {
        $this->model = $model;
    }
}
