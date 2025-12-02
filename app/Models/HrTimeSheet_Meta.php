<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrTimeSheet_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-time-sheet';

    protected static $web_url_admin = '/admin/hr-time-sheet';

    protected static $api_url_member = '/api/member-hr-time-sheet';

    protected static $web_url_member = '/member/hr-time-sheet';

    //public static $folderParentClass = HrTimeSheetFolderTbl::class;
    public static $modelClass = HrTimeSheet::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'n_late' || $field == 'n_hour' || $field == 'n_session' || $field == 'meal') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrTimeSheet edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function _n_late()
    {
        $mm = [0 => 'muộn'];
        $mm[30] = '30p';
        $mm[60] = '60p';
        $mm[90] = '1.5h';
        $mm[120] = '2h';
        $mm[150] = '2.5h';
        $mm[180] = '3h';

        return $mm;
    }

    public static function _n_hour()
    {

        $mm = [0 => 'thêm'];
        $mm[-1] = 'P';
        $mm[-2] = 'O';
        $mm[-3] = 'K';
        $mm[-4] = 'N';

        for ($i = 1; $i <= 16; $i++) {
            $mm[$i] = $i.'h';
        }

        return $mm;

    }

    public static function _meal()
    {
        $mm = [0 => '-Ăn-'];
        $mm['b_1'] = '1b';
        $mm['b_2'] = '2b';
        $mm['b_3'] = '3b';

        return $mm;
    }

    public static function _n_session()
    {
        $mm = [0 => '-Ca-'];
        $mm['n_12'] = 'N';
        $mm['d_12'] = 'Đ';
        $mm['n_10'] = 'N10';
        $mm['d_10'] = 'D10';
        $mm['hc1_8'] = 'HC1';
        $mm['hc2_8'] = 'HC2';
        $mm['hc3_8'] = 'HC3';
        $mm['c_24'] = 'C24';
        $mm['hc_8'] = 'HC';

        return $mm;
    }

    public static function FgetSessionCaArrayTimeSheet()
    {
        $mm = [0 => '-Ca-'];
        $mm['n_12'] = 'N';
        $mm['d_12'] = 'Đ';
        $mm['n_10'] = 'N10';
        $mm['d_10'] = 'D10';
        $mm['hc1_8'] = 'HC1';
        $mm['hc2_8'] = 'HC2';
        $mm['hc3_8'] = 'HC3';
        $mm['c_24'] = 'C24';
        $mm['hc_8'] = 'HC';

        return $mm;
    }

    public static function FgetMealArrayTimeSheet()
    {
        $mm = [0 => '-Ăn-'];
        $mm['b_1'] = '1b';
        $mm['b_2'] = '2b';
        $mm['b_3'] = '3b';

        return $mm;
    }

    public static function FgetHourArrayTimeSheet()
    {

        $mm = [0 => 'thêm'];
        $mm[-1] = 'P';
        $mm[-2] = 'O';
        $mm[-3] = 'K';
        $mm[-4] = 'N';

        for ($i = 1; $i <= 16; $i++) {
            $mm[$i] = $i.'h';
        }

        return $mm;

    }

    //...
}
