<?php

namespace App\Repositories;

use App\Models\TypingLesson;

class TypingLessonRepositorySql extends BaseRepositorySql implements TypingLessonRepositoryInterface
{
    public function __construct(TypingLesson $model)
    {
        $this->model = $model;
    }
}
