<?php

namespace App\Repositories;

use App\Models\QuizChoice;

class QuizChoiceRepositorySql extends BaseRepositorySql implements QuizChoiceRepositoryInterface
{
    public function __construct(QuizChoice $model)
    {
        $this->model = $model;
    }
}
