<?php

namespace App\Repositories;

use App\Models\TypingTestResult;

class TypingTestResultRepositorySql extends BaseRepositorySql implements TypingTestResultRepositoryInterface
{
    public function __construct(TypingTestResult $model)
    {
        $this->model = $model;
    }
}
