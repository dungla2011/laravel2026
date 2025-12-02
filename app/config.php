<?php

if (!isWindow1()) {
    define('DEF_BASE_FILE_UPLOAD_FOLDER', env('UPLOAD_FOLDER'));
} else {
    define('DEF_BASE_FILE_UPLOAD_FOLDER', 'e:/upload_web');
}

//if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
define('DEF_FILE_PATH_IMPORT_EXCEL', "/var/www/html/public/tool1/_site/event_mng/import_user_from_excel");

//else

define('DEF_GID_ROLE_MEMBER', 3);
define('DEF_GID_ROLE_GUEST', 0);

//Khi API login xong, Client sẽ được send cookie session
//Nếu disable session ở api, thì Khi truy cập API, user lấy từ session sẽ được set = null, API ko thể dùng session này
define('DEF_DISABLE_SESSION_FOR_API', 0);
