<?php

namespace Tests\Feature;

use Tests\TestCase;

class MetaTableTest extends TestCase
{
    public static $respondAfterLogin;

    /**
     * Kiểm tra ko bị trùng lặp key meta:
     */
    public function testMetaDublicate()
    {
        $mm = \Illuminate\Support\Facades\DB::connection('mysql_for_common')->table('model_meta_infos')->select('*')->get();
        $m1 = [];
        foreach ($mm as $obj) {
            //    echo "<br/>\n $obj->id , $obj->table_name_model , $obj->field";
            $key = "$obj->table_name_model-$obj->field";
            if (! isset($m1[$key])) {
                $m1[$key] = 1;
            } else {
                $m1[$key]++;
            }
        }
        foreach ($m1 as $k => $v) {
            $this->assertTrue($v == 1, " Error: dublicate key meta: $obj->table_name_model-$obj->field ");
        }
    }
}
