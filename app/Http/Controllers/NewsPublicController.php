<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\News;

class NewsPublicController extends BaseController
{
    protected News $data;

    public function __construct(News $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function index_public()
    {
        return $this->getViewLayout('newspublic.index_public');

        return view('public.news-index');
    }

    public function news_item()
    {
        return $this->getViewLayout('newspublic.news_item');

        return view('public.news-item');
    }
}
