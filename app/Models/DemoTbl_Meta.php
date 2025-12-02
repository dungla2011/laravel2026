<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Support\Facades\Route;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * - Các hàm tên bắt đầu bằng dấu _ sẽ liên kết mở rộng thông tin cho các trường tương ứng
 * + Nếu là: _ + <trên trường trong db>, thì là thông tin bổ xung cho trường đó
 * + Hoặc đặt tên bất kỳ, có thể dùng cho bổ xung cho một bảng Pivot liên kết
 *
 * @param null $objData
 */
class DemoTbl_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/demo';

    protected static $web_url_admin = '/admin/demo-api';

    protected static $api_url_member = '/api/member-demo';

    protected static $web_url_member = '/member/demo-api';

    public static $folderParentClass = DemoFolderTbl::class;

    public static $modelClass = DemoTbl::class;

    ///////////////////////////////////////////////////////////////////
    //Sample JOIN Other table, and Full Search

//    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
//    {
//        return $x->leftJoin('users', 'user_id', '=', 'users.id')
//            ->addSelect([
//                'users.email AS _email',
//            ]);
//    }
//
//    function _email($obj, $val, $field)
//    {
//        return $val;
//    }

    public function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        //Join với bảng users và bảng roles,  để lấy thông tin user và role
//        return $x->leftJoin('users', 'users.id', '=', 'role_user.user_id')
//            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
//            ->addSelect('users.name as _user_name')
//            ->addSelect('users.email as _email')
//            ->addSelect('roles.name as _role_name');
    }

    public function getFullSearchJoinField()
    {
//        return [
//            'users.name'  => "like",
//            'users.email  => "like"',
//            'roles.name'  => "like",
//        ];
    }


    public function getMapFieldAndDesc()
    {
        return parent::getMapFieldAndDesc();
    }

    //Join với 1 bảng khác
    public function getJoinField()
    {
        $mm = [
            'email1' => [
                'table' => 'users',
                'field' => 'email',
                'field_local' => 'user_id',
                'field_remote' => 'id',
//                'field_local1' => 'event_id',
//                'field_remote1' => 'event_id',
            ],
        ];

        return $mm;
    }

    function _join_tags($obj, $val)
    {

        $obj = clone $obj;
        $obj->refresh();
        $isIndex = null;
        $act = Route::getCurrentRoute()->getActionMethod();
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            $isIndex = 1;
            return;
        }

        $fname_ = basename(__FUNCTION__) . "_";
        $fname = basename(__FUNCTION__);

        // Lấy danh sách tất cả folders mà item này thuộc về
        $itemCat = [];
        if ($obj->id) {
            $itemCat = $obj->$fname()->pluck('tags.id')->toArray();
        }
        $ret1 = implode(', ', $itemCat);

        $mm = Tag::all();
        $ret = "<div style='padding: 10px 20px' class='all_check_many' data-field='$fname'>";
        foreach ($mm as $one) {
            $pad = in_array($one->id, $itemCat) ? 'checked' : '';

            if (! $isIndex) {
                $ret .= " <input type='checkbox' $pad id='input$fname$one->id' value='$one->id'>
  <label style='font-weight: normal' for='input$fname$one->id'> $one->name  </label>  &nbsp;";
            }
        }
        $ret .= '</div>';

//        $ret1 = '123';
        return ['value_post' => $ret1, 'value_show' => $ret];
    }

    public static function getIdReadOnlyIfNotSupperAdmin()
    {
        return 0;
    }

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

        if ($field == 'parent_id' || $field == 'parent2') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/demo-folder';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'string2') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        //
        //        if($field == 'string2'){
        //            $objMeta->is_select = 'joinSl1';
        //        }
        if ($field == 'parent_multi') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/demo-folder';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_multi2') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            //Mẫu: $objMeta->join_api = '/api/demo-folder|pid=3&get_all=0';
            //Mẫu: $objMeta->join_api = '/api/demo-folder|pid=3';
            $objMeta->join_api = '/api/demo-folder|pid=3&get_all=0';
            //            $objMeta->join_api = '/api/demo-folder';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'image_list1' || $field == 'image_list2') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
            //            $objMeta->join_func = 'App\Models\News_Meta::joinFuncImageId';
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //            $objMeta->join_func = 'joinTags';

            //Todo: thử bỏ hàm này đi xem test có lỗi gì ko:
            //Demo edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';

            $objMeta->join_api = '/api/tag-demo/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

        return $objMeta;
    }

    public function getRandIdListField($field = null)
    {
        return [];
        //return ['id', 'parent_id', 'married_with', 'child_of_second_married', 'stepchild_of'];
    }

    public function isNumberField($field)
    {
        $mm = ['number1', 'number2'];
        if (in_array($field, $mm)) {
            return 1;
        }

        return 0;
    }

    public function isStatusField($field)
    {
        $mm = ['status'];
        if (in_array($field, $mm)) {
            return 1;
        }

        return 0;
    }

    /**
     *  Sample:
     * ['parent_id' => DemoTbl::class, 'parent_multi' => DemoFolderTbl:class, 'parent_multi2' => null, 'parent2' => null];
     */
    public function getAllFieldBelongUserId()
    {
        return ['parent_id' => null, 'parent_multi' => null, 'parent_multi2' => null, 'parent2' => null];
    }

    public function isUseRandId()
    {
        return 0;
    }

    //List các ảnh thuộc item
    //    function _id($obj, $val){
    //        return $val;
    //    }

    public function _string2($objData = null, $value = null, $field = null)
    {

        /**
         * // Với trường hợp trong DB ghi luôn value mà ko phải Index, thì có thể kiểu $ret [$month] = $month;
         * $ret[0] = "---";
         * $mm = ['2023-01', '2023-02'];
         * foreach ($mm AS $month){
         * $ret [$month] = $month;
         * }
         */
        $mm = [
            0 => '---',
            1 => 'Hà Nội ',
            2 => 'HCM',
            3 => 'Huế',
            4 => 'Đà nẵng',
            5 => 'Phú Quốc',
        ];

        //Nếu có obj có thì mới trả lại Key=>id
        //Nếu ko, nghĩa là trường hợp Get all để chọn
        if ($objData) {
            if (isset($mm[$value]) && $value) {
                return [$value => $mm[$value]];
            } else {
                return null;
            }
        }

        return $mm;
    }

    public function _user_id($objData, $value = null, $field = null)
    {
        return  User_Meta::search_user_email($objData, $value, $field);
    }

    public function _image_list1($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    /**
     * Cộng hòa xã hội
     *
     * @param null $objData
     * @param null $value
     * @param null $field
     * @return array|string|null
     *
     * API:
     * tag_list_id: add()/update() : post single ID of one Tags or multi ID of many tags (separate with comma, for ex: 2,6,7,...)
     * _tag_list_id: get()/list() : details of tags, with array of tags, include id, name... of tags
     */
    public function _tag_list_id($objData = null, $value = null, $field = null)
    {
        //For search / Filter
        if (!$objData) {
            if ($value) {
                if ($tag = \App\Models\TagDemo::where('id', $value)->first()) {
                    return [$tag->id => $tag->name];
                }
            }
            return null;
        }

        if (is_array($value))
            return $value;

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($value);
//        echo "</pre>";
//        return

        $mTag = TagDemo::whereIn('id', explode(',', $value))->get();
        $retApi = [];
        $ret = '';
        if ($mTag) {
            foreach ($mTag as $tag) {
                $ret .= "<span data-code-pos='ppp1668417957667' data-edit-able='' data-autocomplete-id='$objData->id-$field' class='span_auto_complete' data-item-value='$tag->id' title='Remove this item'>$tag->name [x]</span>";
                $retApi[] = ['id' => $tag->id, 'name' => $tag->name];
            }
        }

        if (Helper1::isApiCurrentRequest()) {
            return $retApi;
        }

        return $ret;

        if (!$objData) {
            if ($value) {
                if ($tag = \App\Models\TagDemo::where('id', $value)->first()) {
                    return [$tag->id => $tag->name];
                }
            }

            return null;
        }

        if ($objData instanceof \App\Models\DemoTbl) ;
        if ($objData instanceof \stdClass) {
            $objData = \App\Models\DemoTbl::where('id', $objData->id)->first();
        }
        //Chuyển sang dùng Relation Laravel, BelongToMany
        //Không dùng chuỗi id cách nhau bởi dấy phẩy nữa, chuỗi đó chỉ có tác dụng ở Update lên server
        $mTag = $objData->joinTags;
//        dump($mTag);
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($mTag);
        //        echo "</pre>";
        //        die();

        if ($objData instanceof DemoTbl) ;
        $objMeta = new DemoTbl_Meta();

        $retApi = [];
        $ret = '';
        if ($mTag) {
            foreach ($mTag as $tag) {
                $ret .= "<span data-code-pos='ppp1668417957667' data-edit-able='' data-autocomplete-id='$objData->id-$field' class='span_auto_complete' data-item-value='$tag->id' title='Remove this item'>$tag->name [x]</span>";
                $retApi[] = ['id' => $tag->id, 'name' => $tag->name];
            }
        }

        if (Helper1::isApiCurrentRequest()) {
            return $retApi;
        }

        return $ret;
    }

    public function _image_list2($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _parent_multi($obj, $valIntOrStringInt, $field)
    {
        return $this->_parent_id($obj, $valIntOrStringInt, $field);
    }

    public function _parent_multi2($obj, $valIntOrStringInt, $field)
    {
        return $this->_parent_id($obj, $valIntOrStringInt, $field);
    }

    public function _parent2($obj, $valIntOrStringInt, $field)
    {
        return $this->_parent_id($obj, $valIntOrStringInt, $field);
    }

    public function _parent_id($obj, $valIntOrStringInt, $field)
    {
//        return " $valIntOrStringInt , $obj->id , $obj->parent ";

        //        if($field == 'parent_multi' || $field == 'parent_multi2')

        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub

        /*

        if(!$valIntOrStringInt)
            return null;

        $cls = get_called_class();


        $objFolder = new static::$folderParentClass;;

        if($objFolder instanceof DemoFolderTbl);

        $ret = '';
        $retApi = [];
        //if(strstr($valIntOrStringInt, ','))
        if($valIntOrStringInt)
        {
            $valIntOrStringInt = trim(trim($valIntOrStringInt,','));
            $mVal = explode(",", $valIntOrStringInt);


            if($mm = $objFolder->whereIn("id", $mVal)->get()){
                foreach ($mm AS $obj) {
                    $mName = $obj->getFullPathParentObj(2);
                    $retApi[$obj->id] = $obj->name;
                    $retApi[$obj->id] = $name0 = implode("/", $mName);;
                    $ret .= "<span class='one_node_name' title='remove this: $obj->id' data-id='$obj->id' data-field='$field'> [x] $name0</span>";
                }
            }

        }

        if(Helper1::isApiCurrentRequest())
            return $retApi;
//        else
//            return "xxxxxx <span title='' class='all_node_name' data-field='$field'>$ret </span>";

        return $ret;
        */
    }

    public function _test01_($obj = null, $valIntOrStringInt = null, $field = null)
    {
        return " OK $field";
    }

    public function extraCssInclude()
    {
        ?>

        <style>
            /* For tester */
            .divTable2Cell .icon_tool_for_field {
                 display: inline-block;
            }
        </style>

        <?php
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>
        <style>
            #dynamic-select-container.select_to_add {
                display: block; /* Ẩn container chứa select */
            }
        </style>
        <div style='margin: ; border: 0px solid #ccc' class="mb-3 bg-white px-2 py-2">
            <button id='open_dialog_add_item' class='btn btn-sm btn-default mr-2'> Thêm Item vào</button>
            <button type="button" class="btn btn-sm btn-default" id="inport_from_excel"> Nhập từ Excel</button>
        </div>
        <div id="dynamic-select-container" class="select_to_add mb-3">
            <div id="select_number_item"> Hãy chọn các Item bên dưới để đưa vào Danh sách sau</div>
            <div class="row">
                <div class="col-md-9">
                    <select id="dynamic-select" placeholder="Choose an option...">
                        <option value="" disabled selected> --- Chọn Folder để thêm ---</option> <!-- Title ban đầu -->
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" style=" width: 100%" id="add_items_to_folder">
                        <i class="fa fa-plus"></i>
                        Thêm Mục đã chọn vào Folder này
                    </button>
                </div>

            </div>

        </div>

        <?php
    }

    public function extraJsInclude()
    {
        ?>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                let isChoicesInitialized = false;

                const loadButton = document.getElementById('open_dialog_add_item');
                const selectElement = document.getElementById('dynamic-select');

                getListItemSelect();

                function addUserToEvent(mmSelectId, mmEventCheck) {
                    showWaittingIcon();
                    let user_token = jctool.getCookie('_tglx863516839');

                    let url = "/api/addSomeThingTo";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: {mmSelectId: mmSelectId, mmEventCheck: mmEventCheck},
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if (data.payload) {
                                console.log(" get all Data ", data.payload);
                                alert(data.payload);
                                // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                            } else {
                                alert("Có lỗi xảy ra: \n", JSON.stringify(data))
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx ", data);
                            if (data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                        }
                    });
                }

                $("#add_items_to_folder").on('click', function () {
                    let select = document.getElementById('dynamic-select');
                    let selectedOption = select.options[select.selectedIndex];
                    console.log('Selected option:', selectedOption.value, selectedOption.text);

                    if (!selectedOption.value) {
                        alert("Chưa chọn Folder!")
                        return;
                    }
                    // alert('Selected option: ' + selectedOption.text);

                    let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                    console.log(" mmSelectId = ", mmSelectId);
                    let mmEventCheck = [];

                    // $("input.input_check_eml:checked").each(function () {
                    //     mmEventCheck.push(this.getAttribute('data-id'));
                    // })
                    mmEventCheck.push(selectedOption.value);
                    console.log("Add cmd , mmEvent = ", mmEventCheck);
                    addUserToEvent(mmSelectId, mmEventCheck);

                });


``
                function getListItemSelect() {
                    // Chỉ khởi tạo Choices.js một lần
                    isChoicesInitialized = true;
                    const choices = new Choices(selectElement, {
                        shouldSort: false, // Không sắp xếp lại danh sách
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholder: true, // Bật hỗ trợ placeholder
                        itemSelectText: 'Select',
                    });

                    // Fetch dữ liệu từ API và thêm vào select
                    // fetch('https://jsonplaceholder.typicode.com/users')
                    fetch(
                        '<?php
                            if (Helper1::isMemberModule())
                                echo '/api/member-event-info/list?soby_s1=desc&limit=30';
                            else
                                echo '/api/event-info/list?soby_s1=desc&limit=30';
                            ?>')
                        .then(response => response.json())
                        .then(data => {

                            console.log(" data = ", data);
                            // Kiểm tra cấu trúc dữ liệu trả về
                            if (data.code === 1 && data.payload?.data) {
                                // Trích xuất và MAP dữ liệu
                                const options = data.payload.data.map(item => ({
                                    value: item.id,
                                    label: "(" + item.id + `) ` + item.name,
                                }));
                                // Đưa vào Choices.js
                                choices.setChoices(options, 'value', 'label', true);
                            } else {
                                console.error('Invalid API response:', data);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });

                }

                loadButton.addEventListener('click', () => {

                    let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                    if (!mmSelectId.length) {
                        $("#dynamic-select-container").hide();
                        alert("Bạn chưa chọn danh sách user để thêm? Hãy chọn với check box!");
                        return;
                    }

                    $("#select_number_item").html(" <i class='fa fa-users'></i> Đã chọn: <b>" +
                        mmSelectId.length + " thành viên</b>, hãy chọn Sự kiện để thêm vào:");

                    // Hiển thị select container
                    $("#dynamic-select-container").toggle();

                    if (isChoicesInitialized) {
                        return;
                    }
                    // Chỉ khởi tạo Choices.js một lần
                    isChoicesInitialized = true;
                    getListItemSelect();

                });
            });
        </script>
        <?php
    }
}
