<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class ChangeLog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/change-log';

    protected static $web_url_admin = '/admin/change-log';

    protected static $api_url_member = '/api/member-change-log';

    protected static $web_url_member = '/member/change-log';

    //public static $folderParentClass = ChangeLogFolderTbl::class;
    public static $modelClass = ChangeLog::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'change_log') {
            //            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //ChangeLog edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function _user_id($obj, $val = null, $field = null)
    {
        return (new HrEmployee_Meta())->_user_id($obj, $val, $field);
    }

    public function extraCssIncludeEdit()
    {
        ?>
        <style>
            div[data-field-div="change_log"] .one_item_edit {
                width: 90%;
                max-width: 90%;
            }
            .glx01 {
                width:100%;
            }
            .glx01 {
                margin-top: 10px!important;
            }
            .glx01 table, .glx01 textarea{
                width:100%;
                height: 500px;
            }
            .glx01 td{
                vertical-align: top;
            }
        </style>
        <?php
    }

    public function _change_log($obj, $val)
    {

        $meta = MetaOfTableInDb::getMetaObjFromTableName($obj->tables);

        try {
            unserialize($val);
        } catch (\Throwable $e) { // For PHP 7

            return $val;

        } catch (\Exception $exception) {

            return $val;

        }

        if ($obj->cmd == 'delete') {
            return;
        }

        $ret = '<table class="glx01" data-code-pos="ppp17256701693511">';
        $ret .= '<tr> <th> Field </th> <th> Giá trị cũ </th> <th> Giá trị mới </th> </tr>';
        if ($val) {
            $maxShow = 200;
            $m1 = unserialize($val);
            if ($m1 && is_array($m1)) {
                foreach ($m1 as $k => $v) {
                    $from = $to = '';
                    if (isset($v['from'])) {
                        $from = $v['from'];
                    }
                    if (isset($v['to'])) {
                        $to = $v['to'];
                    }

                    if (getCurrentActionMethod() == 'index') {
                        if (strlen($from) > $maxShow) {
                            $from = " (Dài  hơn $maxShow ký tự, click vào edit để xem chi tiết) ";
                        }
                        if (strlen($to) > $maxShow) {
                            $to = " (Dài  hơn $maxShow ký tự, click vào edit để xem chi tiết) ";
                        }
                    }
                    $sname = '';
                    if ($meta) {
                        $sname = $meta->getDescOfField($k);
                    }
                    $ret .= "<tr> <td style=''>$k <br>($sname)</td> <td> <textarea title='$from' disabled>$from</textarea> </td> <td> <textarea title='$to' disabled>$to</textarea> </td> </tr>";
                }
            }
        }
        //$ret = trim($ret, '-');
        $ret .= '</table>';

        return $ret;

    }
    //...

    public function extraJsInclude()
    {
        ?>
        <style>
            table.glx01 textarea {
                min-width: 60px;
            }
        table.glx01 { border: 1px #ccc solid; border-collapse: collapse; margin: 0px 0px 0px 0px; }
        table.glx01 td {font-size: small;  border: 1px #ccc solid;      padding: 3px 5px 3px 5px;     ;}
        table.glx01 th {font-size: small;  border: 1px #ccc solid;    padding: 3px 5px 3px 5px; background-color: lavender    ;}
        table.glx01 tr:nth-child(even) {background:   #eee }
        table.glx01 tr:nth-child(odd) {background:  lavender  }
        </style>

<?php
    }
}
