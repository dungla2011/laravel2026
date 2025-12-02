<?php

namespace App\Repositories;

use App\Models\QuizFlashCard;

class QuizFlashCardRepositorySql extends BaseRepositorySql implements QuizFlashCardRepositoryInterface
{
    public function __construct(QuizFlashCard $model)
    {
        $this->model = $model;
    }
}
