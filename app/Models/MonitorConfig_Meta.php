<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class MonitorConfig_Meta extends MetaOfTableInDb
{
    public static $api_url_admin = "/api/monitor-config";
    public static $web_url_admin = "/admin/monitor-config";

    public static $api_url_member = "/api/member-monitor-config";
    public static $web_url_member = "/member/monitor-config";

    //public static $folderParentClass = MonitorConfigFolderTbl::class;
    public static $modelClass = MonitorConfig::class;

    /**
     * @param $field
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field){

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if($field == 'alert_type'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if($field == 'alert_config'){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //MonitorConfig edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\MonitorConfigFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\MonitorConfigFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }
    //...

    function getPreHtmlValueEditField($objData, $field) {

        if($field == 'alert_config'){
            ?>
            <div class='pre_html_edit_field'>
            <div style='padding: 10px; font-size: small'>
            <?php
            $valEmail = $valTeleIdGroup = $valTeleToken = $valWebhook = $valSms = '';
            if($objData->alert_type == 'telegram') {
                $mm = explode(",",$objData->alert_config);
                if($mm){
                    $valTeleIdGroup = isset($mm[0]) ? $mm[0] : '';
                    $valTeleToken = isset($mm[1]) ? $mm[1] : '';
                }
            }
            if($objData->alert_type == 'email')
                $valEmail = $objData->alert_config;
            if($objData->alert_type == 'webhook')
                $valWebhook = $objData->alert_config;
            if($objData->alert_type == 'sms')
                $valSms = $objData->alert_config;

            {
                ?>
                <div class="telegram_option">
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 5px 10px 5px 0; vertical-align: middle; width: 150px; font-weight: normal;'><?php echo __('monitor.telegram_chat_id'); ?>:</td>
                            <td style='padding: 5px 0;'>
                                <input class="telegram_info" id='chat_id' value="<?php echo strip_tags($valTeleIdGroup) ?>" style='width: 500px;
                            padding: 5px; border: 1px solid #ddd; border-radius: 3px;' placeholder='<?php echo __('monitor.get_from_botfather'); ?>'>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 10px 5px 0; vertical-align: middle; width: 150px; font-weight: normal;'><?php echo __('monitor.telegram_bot_token'); ?>:</td>
                            <td style='padding: 5px 0;'>
                                <input class="telegram_info" id='bot_token' value="<?php echo strip_tags($valTeleToken) ?>" style='width: 500px;
                            padding: 5px; border: 1px solid #ddd; border-radius: 3px;' placeholder='<?php echo __('monitor.get_from_botfather_35_char'); ?>'>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php
            }
            ?>

            <?php

            {
                ?>
                <div class="email_option">
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 5px 10px 5px 0; vertical-align: middle; width: 150px; font-weight: normal;'><?php echo __('monitor.enter_email'); ?>:</td>
                        <td style='padding: 5px 0;'><input class="email_info" id='email_info' value="<?php echo strip_tags($valEmail) ?>" style='width: 500px; padding: 5px; border: 1px solid #ddd; border-radius: 3px;'
                                                           placeholder='<?php echo __('monitor.enter_valid_email'); ?>'></td>
                    </tr>
                </table>
                </div>
                <?php
            }
            ?>

            <?php

            {
                ?>
                <div class="webhook_option">
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 5px 10px 5px 0; vertical-align: middle; width: 150px; font-weight: normal;'><?php echo __('monitor.webhook_url'); ?>:</td>
                        <td style='padding: 5px 0;'><input class="webhook_info" id='webhook_url' value="<?php echo strip_tags($valWebhook) ?>" style='width: 500px; padding: 5px; border: 1px solid #ddd; border-radius: 3px;'
                                                           placeholder='<?php echo __('monitor.enter_webhook_url'); ?>'></td>
                    </tr>
                </table>
                </div>
                <?php
            }
            ?>

            <?php

            {
                ?>
                <div class="sms_option">
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 5px 10px 5px 0; vertical-align: middle; width: 150px; font-weight: normal;'><?php echo __('monitor.phone_number'); ?>:</td>
                        <td style='padding: 5px 0;'><input class="sms_info" id='phone_number' value="<?php echo strip_tags($valSms) ?>" style='width: 500px; padding: 5px; border: 1px solid #ddd; border-radius: 3px;'
                                                           placeholder='<?php echo __('monitor.enter_phone_number'); ?>'></td>
                    </tr>
                </table>
                </div>
                <?php
            }
            ?>

            </div>
            </div>
            <?php
        }
    }

    function _alert_type($obj, $val, $field){
        $mm = [
            0 => __('monitor.select'),
//            'email' => __('monitor.send_email'),
//            'sms' => __('monitor.send_sms'),
            'telegram' => __('monitor.send_telegram'),
            'webhook' => __('monitor.call_webhook'),
        ];

        return $mm;
    }

    public function extraCssIncludeEdit()
    {
        ?>
        <style>
            .alert_config.input_value_to_post.text_area_edit {
                display: none;
            }
        </style>
        <?php
    }

    public function extraJsIncludeEdit($objData = null)
    {
        ?>

        <script>
            $(document).ready(function () {

                let text_area = $(".alert_config.input_value_to_post.text_area_edit");

                function setPlaceHolder($type){
                    let email_option = $('.email_option');
                    let telegram_option = $('.telegram_option');
                    let webhook_option = $('.webhook_option');
                    let sms_option = $('.sms_option');
                    // if(pre_html_edit_field)
                    //     pre_html_edit_field.css('display', 'none');
                    let sl1 = $('div[data-namex2="alert_config"]');
                    sl1.html("...");

                    console.log("Type = ", $type);



                    // text_area.css('display', 'none');

                    if($type == 'email') {
                        email_option.css('display', 'block');
                        telegram_option.css('display', 'none');
                        webhook_option.css('display', 'none');
                        sms_option.css('display', 'none');
                        text_area.attr("placeholder", "<?php echo strtoupper(__('monitor.js_enter_email')); ?>");
                        sl1.html("<?php echo __('monitor.js_enter_valid_email'); ?>");
                        sl1.css('color', 'red');
                        let val = $("#email_info").val();
                        text_area.val(val);
                    }
                    if($type == 'telegram') {
                        email_option.css('display', 'none');
                        telegram_option.css('display', 'block');
                        webhook_option.css('display', 'none');
                        sms_option.css('display', 'none');
                        text_area.attr("placeholder", "<?php echo strtoupper(__('monitor.js_enter_telegram_info')); ?>");
                        sl1.html("<?php echo __('monitor.js_enter_telegram_id_token'); ?>");
                        sl1.css('color', 'red');
                        let bot_token = $("#bot_token").val();
                        let chat_id = $("#chat_id").val();

                        let val = chat_id +  "," + bot_token;
                        console.log("valx = ", val);
                        text_area.val(val);

                    }
                    if($type == 'webhook') {
                        email_option.css('display', 'none');
                        telegram_option.css('display', 'none');
                        webhook_option.css('display', 'block');
                        sms_option.css('display', 'none');
                        text_area.attr("placeholder", "<?php echo strtoupper(__('monitor.js_enter_webhook_url')); ?>");
                        sl1.html("<?php echo __('monitor.js_enter_valid_webhook_url'); ?>");
                        sl1.css('color', 'red');
                        let val = $("#webhook_url").val();
                        text_area.val(val);
                    }
                    if($type == 'sms') {
                        email_option.css('display', 'none');
                        telegram_option.css('display', 'none');
                        webhook_option.css('display', 'none');
                        sms_option.css('display', 'block');
                        text_area.attr("placeholder", "<?php echo strtoupper(__('monitor.js_enter_phone_number')); ?>");
                        sl1.html("<?php echo __('monitor.js_enter_valid_phone_number'); ?>");
                        sl1.css('color', 'red');
                        let val = $("#phone_number").val();
                        text_area.val(val);
                    }

                    if($type == '0') {
                        console.log("Not type??");
                        email_option.css('display', 'none');
                        telegram_option.css('display', 'none');
                        webhook_option.css('display', 'none');
                        sms_option.css('display', 'none');
                    }
                }
                //Gán sự kiện cho checkbox alert
                setPlaceHolder($('.sl_option.alert_type ').val());

                $('.sl_option.alert_type ').change(function () {
                    $type = $(this).val();
                    setPlaceHolder($type);
                });

                $(".telegram_info").keyup(function(){
                    let bot_token = $("#bot_token").val();
                    let chat_id = $("#chat_id").val();
                    let val = chat_id + "," + bot_token;
                    console.log("val=", val);
                    text_area.val(val);
                });

                $(".email_info").keyup(function(){
                    let val = $("#email_info").val();
                    text_area.val(val);
                });

                $(".webhook_info").keyup(function(){
                    let val = $("#webhook_url").val();
                    text_area.val(val);
                });

                $(".sms_info").keyup(function(){
                    let val = $("#phone_number").val();
                    text_area.val(val);
                });


            });
        </script>

<?php
    }




}
