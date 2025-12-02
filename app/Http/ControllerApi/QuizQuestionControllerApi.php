<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizQuestionRepositoryInterface;
use Illuminate\Http\Request;

class QuizQuestionControllerApi extends BaseApiController
{
    public function __construct(QuizQuestionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function postChoiceOfQues(Request $request)
    {

        echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
        print_r($request->all());
        echo '</pre>';

    }
}
