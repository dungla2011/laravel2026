
<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'kiemtra.mytree.vn';

require_once '/var/www/html/public/index.php';
require_once '/var/www/html/vendor/_ex/simple_html_dom.php';
require_once '/var/www/html/app/Components/qA_W3School.php';

if (! isAdminLrv_()) {

    exit('Not admin!');

}

qA_W3School::showIframe($_GET['qid']);
