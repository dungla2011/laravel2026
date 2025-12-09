<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$str = '<p>Dear <strong>Dr. Nguyen Hung Son</strong>,<br /><br />The Diplomatic Academy of Vietnam 
cordially invites you to attend <strong>Hội thảo “Đánh giá tình hình thế giới và khu vực năm 2025,
 dự báo 2026 và một số vấn đề đặt ra với Việt Nam”</strong>.<br />
 Event details:<br /><br />• Time: <strong>08:00 11/12/2025</strong> to <strong>
 12:00 11/12/2025</strong><br />
 • Venue: <strong>Phòng Geneva, tầng 3, Nhà D, Học viện Ngoại giao</strong><br />###• Topic:<br />###• Language:<br /><br />
 To confirm your participation, please register via the following link: 
 <a target="_blank" href="https://events.dav.edu.vn/user-confirm-event/id/gg447825/data_ev/ms3300060701" rel="noopener"> 
 Confirm attendance</a>.<br />###For further information, please contact [Name], at [Phone Number], 
 or via email: [Email].<br />
 The Diplomatic Academy of Vietnam sincerely appreciates your interest and looks forward to welcoming you at Hội thảo “Đánh giá tình hình thế giới và khu vực năm 2025, 
 dự báo 2026 và một số vấn đề đặt ra với Việt Nam”.<br /><br /><br /><br /><br /></p>';

echo removeCommentsWithDOM2($str);

// echo $str;