<?php

namespace App\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Bổ xung các tham số vào Request DB:
 * Để giới hạn phạm vi các quyền CURD của user, group (role) ở web member, web admin, api member, api admin
 */
class clsParamRequestEx
{
    public $userIdLogined = null;

    public $userObjLogined = null;

    //set_user_id nếu có SET, thì dùng trong trường hợp chỉ cho CURD trên record của user, ko được phép CURD trên record của user khác
    //Luôn để 1 để giới hạn, ở đâu cần thì mở ra
    public $need_set_uid = -1;

    //Nếu đổi field user_id thì đổi ở đây, mặc định là user_id
    //Ví dụ trường hợp DownloadLog sẽ có 2 bảng log: file được tải bởi user và file user được tải
    // user_id download, và user_id_owner của file
    //Thì sẽ set param trên url, để có thể view thêm trường hợp user_id_owner (file user được download)
    public $set_field_uid;

    //có trường hợp cho phép force bỏ qua check uid
    public $ignore_check_userid = 0;

    //Gid (RoleId) dùng để lọc các trường cho phép index, edit, sort... theo GID (ROLE)
    //Mặc định để là quyền Member thôi, ở đâu cần admin thì set lại = 1...
    public $set_gid = 3;

    //Module là admin, hoặc member , dùng trong View, để trỏ đúng các URL UI tương ứng
    public $module;

    //Giới hạn bản ghi hiển thị, cần ko? vì đã có trong params request
    public $limit;

    public $is_api = 0;

    //Bổ xung để hàm editget có thể lấy được obj model luôn
    public $return_laravel_type = 0;

    public $total_item;

    public function __construct($request = null)
    {
        if (! $request) {
            $request = \request();
        }

        $user = getCurrentUserId(1);
        $this->userIdLogined = $user->id ?? null;
        $this->userObjLogined = $user;


        $module = 'admin'; //mặc định chặt chẽ là admin, nếu ko có dấu hiệu gì trên URL

        if (Helper1::isMemberModuleApi($request) || Helper1::isMemberModule($request)) {
            $module = 'member';
        }

        $this->module = Helper1::getModuleCurrentName($request);

        //Mẫu dùng chung, nếu cần Setting lại ở controller nào sử dụng:
        if (Helper1::isMemberModule($request)) {
            $this->set_gid = 3;
            $this->need_set_uid = -1;
        } else {
            $this->set_gid = 1;
            $this->need_set_uid = 0;
        }

        if (str_starts_with($request->route()?->uri(), 'api/')) {
            $this->is_api = 1;
        }
    }

    function getLanguage()
    {
        $lang = $this->userObjLogined->language ?? '';
        return $lang;
    }

    public function setUidIfMust()
    {
        //Nếu nó chưa được set thì mới set
        if ($this->need_set_uid < 0) {
            //không lấy được UID ở controller, nên đảnh lấy ở đây, gọi trong Model thì lấy được UID
            $this->need_set_uid = Auth::id();
        }

        //Có trường hợp lạ với testApiGetFileNotBelongUser
        //nếu ko có đoạn này thì sẽ gây lỗi
        if ($this->need_set_uid > 0 && $this->need_set_uid != Auth::id()) {
            $this->need_set_uid = Auth::id();
        }

        //        echo "\n setUidIfMust: $this->need_set_uid / " . Auth::id();
    }

    public function setParamsEx(Request $request)
    {

    }

    public function isMemberModule()
    {
        if ($this->module == 'member') {
            return 1;
        }

        return 0;
    }
}
