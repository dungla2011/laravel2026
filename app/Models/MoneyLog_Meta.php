<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MoneyLog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/money-log';

    protected static $web_url_admin = '/admin/money-log';

    protected static $api_url_member = '/api/member-money-log';

    protected static $web_url_member = '/member/money-log';

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);
        $objMeta->dataType = $objSetDefault->dataType;

        if ($field == '_join_money_tag') {
            $objMeta->join_api_field = 'name';
            //            $objMeta->join_func = 'joinTags';
            $objMeta->join_relation_func = 'joinMoneyTags';

            $objMeta->join_api = '/api/money-tag/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    /**
     * Thêm meta field này để lấy tag cho money
     * Trả lại cho API
     */
    public function _join_money_tag($obj, $val, $field)
    {
        $ret = [];
        foreach ($obj->joinMoneyTags as $tag) {
            $ret[] = ['id' => $tag->id, 'name' => $tag->name];
        }

        return $ret;
    }
}
