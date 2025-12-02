<?php

namespace App\Repositories;

use App\Models\QuizFolder;

class QuizFolderRepositorySql extends BaseRepositorySql implements QuizFolderRepositoryInterface
{
    public function __construct(QuizFolder $model)
    {
        $this->model = $model;
    }
}
