<?php
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Testing\TestResponse;

//define("DEF_TEST_API_USER", );
//define("DEF_TEST_API_PW", env("PW_4S_TEST"));

class new4STest extends \Tests\Feature\TestCase1 {
    /**
     * @var TestResponse
     */
    public function getAfterLoginResponse()
    {
        return self::$respondAfterLogin;
    }

    static public function getToken4S($user = null, $pw = null)
    {

        if(!$user && !$pw){
            $user = env("USER_4S_TEST");
            $pw = env("PW_4S_TEST");
        }

        $url = "https://v2.4share.vn/api/login-api";
        $data = array(
            'email' => $user,
            'password' => $pw
        );


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response)->payload;
    }

    public function test4sDownloadSession()
    {
        if(!isWindow1())
            return;
        $ret = testDownloadSession4s();
        $this->assertEquals($ret, 3);
    }

    public function test4sDownloadResume2024()
    {

        //Todo: lad stop tam thoi de CI CD
        ///////////////////////////////////////////
        return;

        $link = "https://sv18.4share.vn/81/?test_download_resume_func=1";

        function getFileSizeUrl($link) {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $headers = get_headers($link, 1, $context);

            if (isset($headers['Content-Length'])) {
                return $headers['Content-Length'];
            } else {
                die("Size not found!");
            }
        }

        $filesize = getFileSizeUrl($link);
        $partSize = $filesize / 8; // Kích thước mỗi phần

        $ranges = [];
        for ($i = 0; $i < 8; $i++) {
            $start = $i * $partSize;
            $end = ($i + 1) * $partSize - 1;
            if ($i == 7) {
                $end = $filesize - 1; // Đảm bảo phần cuối cùng tải xuống toàn bộ phần còn lại của tệp
            }
            $ranges[] = [$start, $end];
        }

        echo "\n FS = $filesize";

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($ranges);
//echo "</pre>";

        $strCont = null;

        foreach ($ranges AS $one){
            $start = $one[0];
            $end = $one[1];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_RANGE, $start . '-' . $end);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bỏ qua xác thực SSL
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Bỏ qua xác thực SSL
            $result = curl_exec($ch);
            curl_close($ch);
// Lưu phần tải xuống vào tệp

            $strCont .= $result;
        }
        file_put_contents('e:/part_of_file.zip', $strCont);
        echo "<br/>\nMD5=";
        echo md5($strCont);
        $this->assertEquals(md5($strCont), '1a4d9cc3f520096af2e226385cb48af6');

    }

    static function getFolderList($folderId, $cmd = 'list_file_user'){
        if(!isWindow1())
            return;
        $link = "https://4share.vn/api/v1?cmd=$cmd&folder_id=$folderId";

        $tk = self::getToken4S();
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Authorization: Bearer ' . $tk
//        ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: _tglx863516839=$tk; "));
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    /**
     * - Truy cap folder Khacs root
     * - Folder Root
     * - Id không mã hóa thì không được phép
     *
     * @return void
     * @throws Exception
     */
    function test4sApiGetUserListFile()
    {
        if(!isWindow1())
            return;

        $folderId = '7d4845444a4f4c4b';
        $response = self::getFolderList($folderId);
        $this->assertTrue(str_contains( $response, 'Windows'));
        $this->assertTrue(str_contains($response, 'VMware'));

        $folderId = 0;
        $response = self::getFolderList($folderId);
        $this->assertTrue(str_contains( $response, 'ChromeSetup'));


        $folderId = '123';
        $response = self::getFolderList($folderId);
        $this->assertTrue(str_contains( $response, 'Not valid folder_id'));
    }


    // Folder list tra lai ok:
    function test4SApiListFolderShare()
    {
        if(!isWindow1())
            return;
        $ret = file_get_contents('https://v2.4share.vn/api/v1?cmd=list_folder_in_folder_share&folder_id=7d4845444a4f4c4b');

        $this->assertTrue(str_contains( $ret, 'payloadEx'));

    }

    function test4SApiListFolderUser()
    {
        if(!isWindow1())
            return;
        $ret = self::getFolderList('7d4845444a4f4c4b', 'list_folder_in_folder_user');

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($ret);
        echo "</pre>";

        $this->assertTrue(str_contains( $ret, 'payloadEx'));
    }

    function test4sUserInfo(){
        if(!isWindow1())
            return;
        $tk = self::getToken4S();

//        sleep(2);
        $link = "https://v2.4share.vn/api/v1/?cmd=get_user_info";

        $options = [
            'http' => [
                'method' => "GET",
//                'header' => "accesstoken01: $tk\r\n"
                'header' => "accesstoken01: $tk\r\nCookie: _tglx863516839=$tk\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $ret = file_get_contents($link, false, $context);
        echo $ret;

        $this->assertTrue(str_contains( $ret, 'userid'));
        $this->assertTrue(str_contains( $ret, 'vip_time'));
    }

    function test4sGetFileInfo($fid = '784e49404e404e4d')
    {
        if(!isWindow1())
            return;
        $tk = self::getToken4S();

        $link = "https://v2.4share.vn/api/v1/?cmd=get_file_info&file_id=$fid";
        $options = [
            'http' => [
                'method' => "GET",
//                'header' => "accesstoken01: $tk\r\n"
                'header' => "accesstoken01: $tk\r\nCookie: _tglx863516839=$tk\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $ret = file_get_contents($link, false, $context);
        echo $ret;

        $this->assertTrue(str_contains( $ret, 'full_link'));

        return json_decode($ret);
    }

    function test4sGetLinkDownload($fid = '784e49404e404e4d')
    {
        if(!isWindow1())
            return;
        $fi = $this->test4sGetFileInfo($fid);

        $fsize = $fi->payload->size;

        $link = "https://v2.4share.vn/api/v1/?cmd=get_download_link&file_id=$fid";
        $tk = self::getToken4S();

        echo "\n FileSize = $fsize";

        $options = [
            'http' => [
                'method' => "GET",
//                'header' => "accesstoken01: $tk\r\n"
                'header' => "accesstoken01: $tk\r\nCookie: _tglx863516839=$tk\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $ret = file_get_contents($link, false, $context);
        echo $ret;

        $this->assertTrue(str_contains( $ret, 'download_link'));
        $this->assertTrue(str_contains( $ret, 'payload'));

        $js = json_decode($ret);
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($js);
        echo "</pre>";
        $fileSize2 = get_filesize_remote($js->payload->download_link);

        self::assertTrue($fileSize2 == $fsize);

    }

    public function test4sUploadFtpFile() {
        if(!isWindow1())
            return;
        $tk = self::getToken4S();
        echo "<br/>\n TK = $tk";
        //Todo: lad tam dung cho CICD
        return;
        $ret = test4SUploadFtpApi($tk);
//        $this->assertTrue($ret == 2);
    }

}
