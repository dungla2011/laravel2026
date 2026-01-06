<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;


class ZaloUser_Meta extends MetaOfTableInDb
{

    public static $folderParentClass = ZaloUser::class;

    public static $modelClass = ZaloUser::class;

    public function extraCssInclude()
    {
        ?>

        <style>
            .input_value_to_post.readonly.list_ip_address{
                display: none;
            }
            .input_value_to_post.readonly.power_state{
                display: none;
            }
        </style>
<?php

    }

    function _list_ip_address($obj, $val, $field){
        $val = str_replace(",", "<br>", $val);
        return "<div style='color: green; font-size: smaller; margin-left: 5px'>$val </div>";
    }
    function _power_state($obj, $val, $field){
        $val1 = "OFF";
        if(str_contains($val, "_ON")){
            $color = "green";
            $val1 = "ON";
        }
        else
            $color = "red";

        return "<div style='color: $color; font-size: smaller; margin-left: 5px; text-align: center'>$val1 </div>";
    }

}
