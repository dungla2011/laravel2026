<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\Tag;
use App\Repositories\TagDemoRepositoryInterface;


class TagsDemoControllerApi extends BaseApiController
{
    //    public function __construct(Tag $data) {
    //        $this->data = $data;
    //    }

    public function __construct(TagDemoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }
}
