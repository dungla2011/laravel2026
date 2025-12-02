<?php

namespace Tests\Feature;

use Tests\TestCase;

class _NeedTest extends TestCase
{
    //Todo: cần test move parent_id, có check parentid thuộc user chứ ko bị move sang user khác
    //- Test api admin delete...
    // Test Path breakum 2 trường hợp:
    //+ /admin/demo-api?seby_s14=1 , tạo item, move các item sang các folder khác nhau và duyệt lại
    //+ /admin/demo-folder?seby_s7=3, tạo item, move các item sang các folder khác nhau và duyệt lại

    //Done Cần test index của từng bảng

    //Cần test cache, xem thêm xóa sửa qua API, cache có hoạt động ko
    //Và thêm tính năng xóa hết cache của user, có thể có 1 cron chạy nền để tự refresh all cache, để tránh sai sót cache

    //GiaPha: kiểm tra delete, udelete UI, xem cache có hoạt động đúng không

    //Upload ảnh trong tinymce, chỗ editor...
    //Test login, register... bỏ qua check capcha nếu testing
}
