<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * - Các hàm tên bắt đầu bằng dấu _ sẽ liên kết mở rộng thông tin cho các trường tương ứng
 * + Nếu là: _ + <trên trường trong db>, thì là thông tin bổ xung cho trường đó
 * + Hoặc đặt tên bất kỳ, có thể dùng cho bổ xung cho một bảng Pivot liên kết
 *
 * @param  null  $objData
 */
class TagDemo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/tag-demo';

    protected static $web_url_admin = '/admin/tag-demo';

    protected static $api_url_member = '/api/member-tag';

    protected static $web_url_member = '/member/tag';

//    public static $folderParentClass = Tag::class;

    public static $modelClass = TagDemo::class;

}
