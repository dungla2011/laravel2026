<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Product;

class ProductPublicController extends BaseController
{
    protected Product $data;

    public function __construct(Product $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function index_public()
    {
        return $this->getViewLayout();

        return $this->getViewLayout('productpublic.index_public');
    }

    public function item()
    {
        return $this->getViewLayout('productpublic.item');

        return $this->getViewLayout();
    }
}
