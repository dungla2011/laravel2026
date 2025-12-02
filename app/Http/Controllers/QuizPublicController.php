<?php

namespace App\Http\Controllers;

class QuizPublicController extends BaseController
{
    public function index()
    {
        return view('quiz.quiz-index');
    }
}
