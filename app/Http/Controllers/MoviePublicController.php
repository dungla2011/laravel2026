<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\News;

class MoviePublicController extends BaseController
{
    protected News $data;

    public function __construct(News $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function cat_view()
    {
//        return $this->getViewLayout('newspublic.index_public');

        return view('public.movie1_cat');
    }

    public function item_view()
    {
//        return $this->getViewLayout('newspublic.news_item');

        return view('public.movie1_item');
    }
}
