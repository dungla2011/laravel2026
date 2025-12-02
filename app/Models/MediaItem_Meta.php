<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Support\Facades\Route;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MediaItem_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/media-item';

    protected static $web_url_admin = '/admin/media-item';

    protected static $api_url_member = '/api/member-media-item';

    protected static $web_url_member = '/member/media-item';

    public static $folderParentClass = MediaFolder::class;

    public static $modelClass = MediaItem::class;

    public static $allowAdminShowTree = 1;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'options') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //MediaItem edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'description') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/media-folder';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_extra') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/media-folder';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }
        if ($field == '_folders') {
            $objMeta->join_func_model = '_folders';
        }

        if ($field == '_actors') {
            $objMeta->join_func_model = '_actors';
        }

        if ($field == '_authors') {
            $objMeta->join_func_model = '_authors';
        }

        return $objMeta;
    }

    public function _options($obj, $val, $field)
    {
        return $mm = [
            '0' => '-Chọn-',
            '1' => 'Top Trang chủ',
            '2' => 'Cuối trang chủ',
            '3' => 'Option 3',
        ];
    }

    public function _authors($obj, $val, $field)
    {
        $isIndex = null;
        $act = Route::getCurrentRoute()->getActionMethod();
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            $isIndex = 1;
        }

        $fname_ = basename(__FUNCTION__) . "_";
        $fname = basename(__FUNCTION__);

        // Lấy danh sách tất cả folders mà item này thuộc về
        $itemCat = [];
        if ($obj->id) {
            $itemCat = $obj->_authors()->pluck('media_authors.id')->toArray();
        }
        $ret1 = implode(', ', $itemCat);

        $mm = MediaAuthor::limit(50)->get();
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

    public function _actors($obj, $val, $field)
    {
        $isIndex = null;
        $act = Route::getCurrentRoute()->getActionMethod();
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            $isIndex = 1;
        }

        $fname_ = basename(__FUNCTION__) . "_";
        $fname = basename(__FUNCTION__);

        // Lấy danh sách tất cả folders mà item này thuộc về
        $itemCat = [];
        if ($obj->id) {
            $itemCat = $obj->_actors()->pluck('media_actors.id')->toArray();
        }
        $ret1 = implode(', ', $itemCat);

        $mm = MediaActor::limit(50)->get();
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


    public function _folders($obj, $val, $field)
    {
        $isIndex = null;
        $act = Route::getCurrentRoute()->getActionMethod();
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            $isIndex = 1;
        }

        $fname_ = basename(__FUNCTION__) . "_";
        $fname = basename(__FUNCTION__);

        // Lấy danh sách tất cả folders mà item này thuộc về
        $itemCat = [];
        if ($obj->id) {
            $itemCat = $obj->_folders()->pluck('media_folders.id')->toArray();
        }
        $ret1 = implode(', ', $itemCat);

        $mm = MediaFolder::all();
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

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _link_list($obj, $valIntOrStringInt, $field)
    {

        $guide = 'Thêm các link download/play, mỗi link xuống dòng
- Mẫu: Tên link và Link Url cách nhau bởi dấu | (có thể có Tên hoặc không có Tên - nghĩa là chỉ có link)
Tên link | link Url
- Ví dụ:
Phim Doremon - Tập 1 | https://4share.vn/f/doremon1.mkv
Phim Doremon - Tập 2 | https://4share.vn/f/doremon2.mkv
 ';

        $mm = MediaLink::where('media_id', $obj->id)->get();
        $ret = "<table class='glx01' style='width: 100%' title='$guide'>";

        $cc = 0;
        foreach ($mm as $link) {
            $cc++;
            //            echo "\n<tr>";
            //            $ret .= "<td>$cc</td> <td>  $link->id </td> <td> $link->name  </td> <td> <button class='btn btn-sm btn-warning'> Remove  </button> </td>";
            //            echo "\n</tr>";
        }
        $ret .= "\n<tr>";
        $ret .= "<td colspan='3' > <textarea  placeholder='$guide' id='link_to_add' style='width: 100% ;  min-height: 150px; background-color: white' type='text'></textarea> </td> <td> <button id='btn_add_link' type='button' class='btn btn-sm btn-info'> ADD  </button> </td>";
        $ret .= "\n</tr>";
        $ret .= '</table>';

        return $ret;
    }

    public function extraJsIncludeEdit($objData = null)
    {
        ?>
        <script>
            // Bắt sự kiện khi checkbox trong .all_check_many được click


        </script>
        <style>
            .divTable2Cell .readonly_imgs, .divTable2Cell .one_item_edit {
                max-width: none;
            }
        </style>
        <script>
            $(function () {
               $("#btn_add_link").on('click', function (){
                   let linkAdd = document.getElementById('link_to_add').value
                   console.log("Click btn_add_link: " , linkAdd);

                })
            })

        </script>
<?php
    }

    public function _parent_all($obj, $valIntOrStringInt, $field)
    {
        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub
    }

    public function _parent_list($obj, $valIntOrStringInt, $field)
    {
        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub
    }

    public function _parent_id($obj, $valIntOrStringInt, $field)
    {
        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub
    }

    public function _parent_extra($obj, $valIntOrStringInt, $field)
    {
        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub
    }

    //...
}
