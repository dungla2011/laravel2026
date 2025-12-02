<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class OrderShip_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/order-ship';

    protected static $web_url_admin = '/admin/order-ship';

    protected static $api_url_member = '/api/member-order-ship';

    protected static $web_url_member = '/member/order-ship';

    //public static $folderParentClass = OrderShipFolderTbl::class;
    public static $modelClass = OrderShip::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //OrderShip edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _json_get($obj, $val)
    {

        $mm = json_decode($val);
        //         echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //         print_r($mm);
        //         echo "</pre>";
        //         return;
        $mm = $mm->order;
        $ret = '';
        if ($mm) {
            $ret = '<table data-code-pos="ppp1681201325531" class="glx01">';
            foreach ($mm as $k => $v) {
                if (is_object($v) || is_array($v)) {
                    $v = serialize($v);
                }
                $ret .= "<tr> <td> $k </td> <td> $v  </td></tr>";
            }
            $ret .= '</table>';
        }

        return $ret;
    }

    public function _json_send($obj, $val)
    {
        $mm = json_decode($val);
        $ret = '';
        if ($mm) {
            $ret = '<table data-code-pos="ppp1681201325531" class="glx01">';
            foreach ($mm as $k => $v) {
                $ret .= "<tr> <td> $k </td> <td> $v  </td></tr>";
            }
            $ret .= '</table>';
        }

        return $ret;
    }

    public function _log($obj, $val)
    {
        $mm = explode("\n", $val);
        $ret = '';
        foreach ($mm as $line) {
            $ret .= $line.'<br>';
        }

        return $ret;
    }

    //...
}
