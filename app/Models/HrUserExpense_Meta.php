<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrUserExpense_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-user-expense';

    protected static $web_url_admin = '/admin/hr-user-expense';

    protected static $api_url_member = '/api/member-hr-user-expense';

    protected static $web_url_member = '/member/hr-user-expense';

    public static $getMapColField_class = HrExpenseColMng::class;

    //public static $folderParentClass = HrUserExpenseFolderTbl::class;
    public static $modelClass = HrUserExpense::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'num4') {
            //            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrUserExpense edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _num4($field, $replaceDash = 0)
    {
        //        return $mm = [0=>"--", 1=>"x1", 2=>"x2", 3=>"x3", ];
    }

    public function getDescOfField1($field, $replaceDash = 0)
    {
        switch ($field) {
            case 'num1': return 'Điện 1';
                break;
            case 'num3': return 'Điện 2';
                break;
            default: return parent::getDescOfField($field, $replaceDash);
        }
    }

    public static function tinhToan($mDataUidAndTime, $uid, &$mmTotalAll)
    {
        $mTotal = [];
        $mTotal['tien_dien'] = 0;
        $mTotal['tien_nuoc'] = 0;

        $mmTotalAll['tien_dien'] = $mmTotalAll['tien_dien'] ?? 0;
        $mmTotalAll['tien_nuoc'] = $mmTotalAll['tien_nuoc'] ?? 0;

        if ($mDataUidAndTime[$uid] ?? '') {
            foreach ($mDataUidAndTime[$uid] as $obj) {
                $mTotal['tien_dien'] += $obj->num1;
                $mTotal['tien_nuoc'] += $obj->num2;

                $mmTotalAll['tien_dien'] += $obj->num1;
                $mmTotalAll['tien_nuoc'] += $obj->num2;
            }
        }
        echo '<pre>';
        print_r($mTotal);
        echo '</pre>';

        return $mTotal;
    }

    //...
}
