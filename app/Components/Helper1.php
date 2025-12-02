<?php

namespace App\Components;

use App\Models\FileUpload;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

class Helper1
{
    public static function deleteAllPermissionUrlRoute()
    {
        \App\Models\Permission::truncate();
    }

    public static function getIdYoutubeFromUrl($url)
    {
        $url = explode(',', $url)[0];
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/",
            $url,
            $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    public static function getValidateStringAlt($locale = null)
    {
        // Get current locale, fallback to 'vi'
        if (! $locale) {
            $locale = app()->getLocale() ?: 'en';
        }

        $messages = [
            'required' => __('validation.required', [], $locale),
            'string' => __('validation.string', [], $locale),
            'file' => __('validation.file', [], $locale),
            'mimes' => __('validation.mimes', [], $locale),
            'max' => __('validation.max.string', [], $locale),
            'min' => __('validation.min.string', [], $locale),
            'unique' => __('validation.unique', [], $locale),
            'digits_between' => __('validation.digits_between', [], $locale),
            'regex' => __('validation.regex', [], $locale),
            'email' => __('validation.email', [], $locale),
            'confirmed' => __('validation.confirmed', [], $locale),
            'numeric' => __('validation.numeric', [], $locale),
            'integer' => __('validation.integer', [], $locale),
            'boolean' => __('validation.boolean', [], $locale),
            'date' => __('validation.date', [], $locale),
            'in' => __('validation.in', [], $locale),
            'exists' => __('validation.exists', [], $locale),
            'image' => __('validation.image', [], $locale),
            'url' => __('validation.url', [], $locale),
        ];

        return $messages;
    }

    /**
     * Get custom attribute names for validation
     * Customize :attribute placeholder in validation messages
     *
     * @return array
     */
    public static function getValidateAttributeNames()
    {
        $attributes = [
            'name' => 'Tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'description' => 'Mô tả',
            'image' => 'Hình ảnh',
            'file' => 'Tệp tin',
            'user_id' => 'Người dùng',
            'parent_id' => 'Danh mục cha',
            'status' => 'Trạng thái',
            'orders' => 'Thứ tự',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
            // Add more custom attribute names here
        ];

        return $attributes;
    }

    public static function getModuleCurrentName(Request $request = null)
    {
        if(isCli())
            return null;

        if(!$request)
            $request = \request();
        if (str_starts_with($request->route()->uri(), 'api/member')) {
            return 'member';
        }
        if (str_starts_with($request->route()->uri(), 'member')) {
            return 'member';
        }
        if (str_starts_with($request->route()->uri(), 'api/admin')) {
            return 'admin';
        }
        if (str_starts_with($request->route()->uri(), 'admin')) {
            return 'admin';
        }
        if (str_starts_with($request->route()->uri(), '_admin')) {
            return '_admin';
        }

        return 'admin';
    }

    /**
     * Yêu cầu các api member, URL request có dạng api/member... sẽ được phân nhóm là member
     *
     * @return int
     */
    public static function isMemberModuleApi(Request $rq)
    {
        if (str_starts_with($rq?->route()?->uri(), 'api/member')) {
            return 1;
        }

        return 0;
    }

    /**
        URL /member sẽ là module member
     * @return int
     */
    public static function isMemberModule(?Request $rq = null)
    {
        if (! $rq) {
            $rq = \request();
        }
        if (str_starts_with($rq?->route()?->uri(), 'member')) {
            return 1;
        }
        if (str_starts_with($rq?->route()?->uri(), 'api/member')) {
            return 1;
        }

        return 0;
    }

    public static function isAdmin2Module(?Request $rq = null)
    {
        if (! $rq) {
            $rq = \request();
        }
        if (str_starts_with($rq?->route()?->uri(), '_admin')) {
            return 1;
        }
        if (str_starts_with($rq?->route()?->uri(), 'api/_admin')) {
            return 1;
        }

        return 0;
    }

    /**
     * Yêu cầu các api member, URL request phải có dạng api/admin...... sẽ được phân nhóm là admin
     *
     * @return int
     */
    public static function isAdminModuleApi(Request $rq)
    {
        if (!str_contains($rq?->route()?->uri(), 'api/member-')) {
            return 1;
        }
        return 0;
    }

    public static function isApiCurrentRequest()
    {
        $r = request()?->route();
        if (! $r) {
            return;
        }
        if (! $r->uri()) {
            return;
        }
        if (str_starts_with($r?->uri(), 'api/')) {
            return 1;
        }

        return 0;
    }

    public static function isToolCurrentRequest()
    {
        if (str_starts_with(UrlHelper1::getUriWithoutParam(), '/tool1')) {
            return 1;
        }

        return 0;
    }

    /**
     * URL /admin sẽ là module member
     *
     * @return int
     */
    public static function isAdminModule(?Request $rq = null)
    {

        if (! $rq) {
            $rq = \request();
        }

        if (str_starts_with($rq?->route()?->uri(), 'api/admin')) {
            return 1;
        }
        if (str_starts_with($rq?->route()?->uri(), 'admin')) {
            return true;
        }
        if (str_starts_with($rq?->route()?->uri(), '_admin')) {
            return true;
        }

        return false;
    }

    /**
     * @param  $route  Route
     */
    public static function checkValidUriInsertRoute($route)
    {
        if (str_starts_with($route->getName(), 'admin.')
        || str_starts_with($route->uri(), 'member')
        || str_starts_with($route->uri(), 'api/')
        || str_starts_with($route->uri(), 'task')) {
            return 1;
        }

        return 0;
    }

    /**
     * @return mixed
     *               array:15 [
    "driver" => "mysql"
    "url" => null
    "host" => "12.0.0.54"
    "port" => "3306"
    "database" => "..."
    "username" => "..."
     */
    public static function getDBInfo()
    {
        return \Illuminate\Support\Facades\Config::get('database.connections.'.\Illuminate\Support\Facades\Config::get('database.default'));
    }

    /**
     * Tạo bảng permission tự động bằng cách list các route, với 1 số trường customize trong file route web.php
     * để insert vào db, ví dụ trường route_desc_ ...
     */
    public static function createPermissionAutomatic()
    {

        $routeCollection = Route::getRoutes();
        $nUpdateParent = $nInsertParent = 0;
        $nUpdateChild = $nInsertChild = 0;

        //Thêm các cha, là tất cả các prefix
        foreach ($routeCollection as $value) {

            //            echo "<br/>\n " . $value->uri();
            //
            //            continue;

            //Chỉ lấy trong admin, member
            if (! \App\Components\Helper1::checkValidUriInsertRoute($value)) {
                continue;
            }

            //        echo "<br/>\n Pref = " . $value->getPrefix();

            //Xử lý riêng prefix, chính là folder
            $ret = \App\Models\Permission::where('route_name_code', $value->getPrefix())->first();
            $mAdd = [
            ];
            //        dump($ret);
            if (! $ret) {

                $mAdd['prefix'] = $value->getPrefix();
                $mAdd['route_name_code'] = $value->getPrefix();
                $mAdd['url'] = $value->uri();

                if (isset($value->route_group_desc_)) {
                    $mAdd['display_name'] = $value->route_group_desc_;
                } else {
                    $mAdd['display_name'] = $value->getPrefix();
                }

                if (isset($mAdd['display_name']) && $mAdd['display_name']) {
                    if (isset($mAdd['route_name_code']) && $mAdd['route_name_code']) {
                        //                    echo "<br/>\n key_code = " . $mAdd['key_code'];
                        \App\Models\Permission::create($mAdd);
                        $nInsertParent++;
                    }
                }
                //            echo "<br/>\n ADD ";
            } else {
                //            echo "<br/>\n Not ADD ";
                //Nếu có rồi thì xem sửa không:
                if (isset($value->route_group_desc_) && $ret->display_name != $value->route_group_desc_) {
                    $mAdd['display_name'] = $value->route_group_desc_;
                    $ret->update($mAdd);
                    $nUpdateParent++;
                }

            }
        }

        //        return;

        $cc = 0;
        //Thêm các con
        foreach ($routeCollection as $value) {
            if ($value instanceof \Illuminate\Routing\Route);
            if ($value->uri() != 'login') {
            }

            //Chỉ lấy trong admin, member
            if (! \App\Components\Helper1::checkValidUriInsertRoute($value)) {
                continue;
            }

            $cc++;
            $obj = new \App\Models\Permission();

            //Nếu chưa có 1 keycode này, thì thêm
            $ret = \App\Models\Permission::where('route_name_code', $value->getName())->first();

            $mAdd = [
            ];
            if (! $ret) {
                $mAdd['prefix'] = $value->getPrefix();
                if (isset($value->route_desc_)) {
                    $mAdd['display_name'] = $value->route_desc_;
                } else {
                    $mAdd['display_name'] = $value->getName();
                }

                //      dump("Name = " . $value->getName());

                $mAdd['route_name_code'] = $value->getName();
                $mAdd['url'] = $value->uri();
                //Lấy ra Per cha
                if ($parentPer = \App\Models\Permission::where('route_name_code', $value->getPrefix())->first()) {
                    $mAdd['parent_id'] = $parentPer->id;
                }

                if (isset($mAdd['display_name']) && $mAdd['display_name']) {
                    if (isset($mAdd['route_name_code']) && $mAdd['route_name_code']) {
                        $obj->create($mAdd);
                        $nInsertChild++;
                    }
                }
            } else {

                //            dump($ret);

                //            echo "<br/>\n Không Thêm ... , " . $value->getName();

                //Sửa hay không:
                if (isset($value->route_desc_) && $value->route_desc_ != $ret->display_name) {
                    //                echo "<br/>\n Sửa name ...";

                    //     dump("Name = " . $value->route_desc_);

                    $mAdd['display_name'] = $value->route_desc_;
                    $ret->update($mAdd);
                    $nUpdateChild++;
                }

            }
        }

        echo "<div style='color: red'> Kết quả phân tích thêm quyền: Tổng số $cc quyền. Thêm  $nInsertParent cha, $nInsertChild con. Cập nhật $nUpdateParent cha, $nUpdateChild con </div>";
        echo "<br/>\n";
    }

    static function getCurrentActionMethod()
    {
        return \Illuminate\Support\Facades\Route::getCurrentRoute()->getActionMethod();
    }

    public static function imageShow1($obj, $val, $field, $getData = 0)
    {
        if (! $val) {
            return null;
        }

        $mIdFile = explode(',', $val);
        $retApi = [];
        $ret = '';
        $cc = 0;
        $tt = count($mIdFile);
        foreach ($mIdFile as $idF) {

            if(!is_numeric($idF))
                $idF = qqgetIdFromRand_($idF);
            if(!is_numeric($idF))
                continue;

            if ($objFile = FileUpload::find($idF)) {
                $cc++;
                if (getCurrentActionMethod() == 'index') {
                    if ($cc > 2) {
                        break;
                    }
                }

                if ($cc > 50) {
                    $ret .= " <div> [Có lỗi: Không hiển thị hết vì số file lớn ($tt file > 50) ] </div>";
                    break;
                }

                $retApi[] = (object) ['id' => $objFile->id, 'name' => $objFile->name, 'thumb' => $objFile->getCloudLinkImage(), 'link' => $objFile->getCloudLink()];

                if ($objFile instanceof FileUpload);

                //                $ext = pathinfo($objFile->name, PATHINFO_EXTENSION);
                if (
                    $objFile->isImageFileName() ||
                    strstr($objFile->mime, 'image') !== false ||
                strstr($objFile->mime, 'video') !== false||
                strstr($objFile->mime, 'pdf') !== false
                ) {
                    $thumb = $objFile->getCloudLinkImage();
                    $fileImg = "<img style='min-width: 60px; min-height: 40px; border: 1px solid green' src='$thumb' alt='' title='$objFile->name |$objFile->cloud_id , $objFile->created_at'>";
                } else {
                    $fileImg = "<span title='$objFile->name , $objFile->created_at'> [$objFile->name] </span>";
                }

                $link = $objFile->getCloudLink();

                $ret .= "<span data-code-pos='ppp1668242218866' class='img_zone' data-img-id='$objFile->id' >

                <a href='$link' target='_blank'>$fileImg</a>
<span class='one_node_name fa fa-times' title='remove this: $objFile->id'
data-id='$objFile->id' data-field='$field'>  </span> </span>";
            }
        }

        if (getCurrentActionMethod() == 'index') {
            if ($tt > 2) {
                $ret .= " <div style='font-size: small; text-align: center'> [Tổng số ".($tt).' Ảnh/File] </div>';
            }
        }

        if (Helper1::isApiCurrentRequest() || $getData) {
            return $retApi;
        }

        return " <span class='all_node_name sort_able_imgs'>".$ret.'</span>';
    }

    /**
     * @param  string  $module
     * @param  MetaOfTableInDb  $objMeta
     */
    public static function tinyMceEditorInit($id, $module, $objMeta, $objPr)
    {
        ?>
        <script>
            //<?php echo "FIELDx1 : $objMeta->field / ".get_class($objMeta) ?>
            //let idx = '<?php //echo $id?>//'
            //___StartTinyMCE___
            tinymce.init({
                <?php

                if (! $objMeta->isEditableFieldGetOne($objMeta->field, $objPr->set_gid)) {
                    echo 'readonly : 1,';
                }

        //if(\Base\ClassRoute::getCurrentAction() == 'add')
        //if(strstr($id, '_content'))

        if ($h = $objMeta->getHeightTinyMce($objMeta->field)) {
            echo 'height: "'.$h.'",';
        } else {
            echo 'height: "400",';
        }

        ?>
                // skin: 'borderless',
                selector: '<?php echo $id ?>',
                content_style: '.img-responsive-glx {max-width: 1200px; height: auto; }',
                // content_css: '/public/css/font-awesome.min.css',
                fontsize_formats:"6pt 8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 18pt 20pt 22pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt",
                image_dimensions: false,
                //toolbar: "sizeselect | bold italic | fontselect |  fontsizeselect | align | forecolor backcolor link image",
                toolbar:
                    "insertfile a11ycheck undo redo | fontselect formatselect  sizeselect fontsizeselect bold underline italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry",
                plugins: [
                    "anchor autolink codesample colorpicker contextmenu fullscreen help image imagetools",
                    " paste code lists link media noneditable preview",
                    " searchreplace table template textcolor visualblocks wordcount tiny_mce_wiris"
                ], //removed:  charmap insertdatetime print
                //extended_valid_elements : "script[src|async|defer|type|charset],style,link,i[class|style],a[id|href|style|class],img[class|src|style]",
                extended_valid_elements : "pre,style,link,i[class|style],a[id|target|href|style|class],img[class|src|style],",
                custom_elements:"style,link",
                //valid_children: 'pre[code|ul|p|li|span|a|div],p[strong|img|a]',
                entity_encoding : "raw",
                 external_plugins: { tiny_mce_wiris: '/vendor/tinymce/mathtype-tinymce4/plugin.min.js' },
                mode :             'textareas',
                force_br_newlines: false,
                force_p_newlines:   false,
                forced_root_block:  '',

                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                paste_data_images: true,relative_urls : false,
                remove_script_host : true,
                document_base_url : "/",
                convert_urls : false,
                image_class_list: [
                    {title: 'img_title_', value: 'img-responsive-glx'}
                ],

                //images_upload_url: 'postAcceptor.php',
                //images_upload_url: '/test/test03.php',
                //images_upload_url: clsUpload.url_server,
                images_upload_handler: function (blobInfo, success, failure) {
                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    xhr.open('POST', '/api/member-file/upload');
                    xhr.setRequestHeader('Authorization', 'Bearer ' + jctool.getCookie('_tglx863516839'));

                    xhr.onload = function() {
                        var json;
                        if (xhr.status != 200) {
                            failure('HTTP Error: ' + xhr.status);
                            return;
                        }
                        json = JSON.parse(xhr.responseText);
                        console.log(" JSON RET = ", json);
                        // if (!ClassApi.checkReturnApi(json))
                        //     return;
                        // if (!json || typeof json.location != 'string') {
                        //     failure('Invalid JSON: ' + xhr.responseText);
                        //     return;
                        // }
                        //success(json.location);

                        //Dòng này để insert link img vào, thay vì <img data=blog...
                        success(json.payload.link);
                    };
                    formData = new FormData();
                    formData.append('file_data', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },
                //Demo Filepiker: https://www.tiny.cloud/docs/demo/file-picker/
                file_picker_callback: function (callback, value, meta) {
                    myImagePicker(callback, value, meta);
                }
            });
            //2020-08-17 dành cho paste
            // tinymce.activeEditor.uploadImages(function(success) {
            //   $.post('ajax/post.php', tinymce.activeEditor.getContent()).done(function() {
            //         console.log("Uploaded images and posted content as an ajax request.");
            //   });
            // });

            ////https://stackoverflow.com/questions/16780328/how-should-i-update-a-tinymce-plugin-using-tiny-mce-popup-js-for-version-4/24571800
            function myImagePicker(callback, value, meta) {

                console.log("picker image...");

                $("#id-iframe-browser-file").attr('data-cmd', 'call_for_tiny_editor')
                $("#id-iframe-browser-file").prop('data-cmd', 'call_for_tiny_editor')

                <?php

        $obj = new News();
        $obj2 = News::getMetaObj();
        $sname = $obj2->getShortNameFromField('created_at');

        ?>

                tinymce.activeEditor.windowManager.open({
                        title: 'Chọn ảnh từ Thư viện',
                        //url: '/test/show_list_image_tinymce.php',
                        //qqq02938409237409269
                        <?php
                if ($module == 'admin') {
                    ?>
                        url: '/admin/file?browse_file_iframe=1&limit=16',
                        <?php
                } else {
                    ?>
                        url: '/member/file?browse_file_iframe=1&limit=16',
                        <?php
                }
        ?>
                        width: 900,
                        height: 500,
                        buttons: [
                            // {
                            //     text: 'Insert',
                            //     onclick: function () {
                            //         //do some work to select an item and insert it into TinyMCE
                            //         console.log(" onclick insert ");
                            //         ////////////////
                            //         //https://stackoverflow.com/questions/16780328/how-should-i-update-a-tinymce-plugin-using-tiny-mce-popup-js-for-version-4/24571800
                            //     }
                            // },
                            {
                                text: 'Close',
                                onclick: 'close'
                            }
                        ],
                    },
                    {
                        oninsert: function (url) {
                            console.log(" insert url: " + url);
                            callback(url);
                        }
                    });
            };

        </script>


        <?php
    }
}
