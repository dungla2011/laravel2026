<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\AiChatConversationRepositoryInterface;

class AiChatConversationControllerApi extends BaseApiController
{
    public function __construct(AiChatConversationRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        //Nếu =0, là ko set quyen UID nao ca
//        $objPrEx->need_set_uid = 0;
        parent::__construct();
    }
}
