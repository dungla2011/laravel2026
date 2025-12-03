<?php


use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Testing\TestResponse;

class ApiUploadFileTest1 extends \Tests\Feature\TestCase1
{
    /**
     * @var TestResponse
     */
    protected function setUp(): void
    {
        \Tests\Feature\TestCase1::setUp();
        // set your headers here
        if (!User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
        if (!User::where('email', 'member@abc.com')->first()) {
            User::createUserMemberForTest();
        }
        $tk = User::where("email", 'admin@abc.com')->first()->getUserToken();
        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);
    }

    public function testUploadRealFile($delete = 1, $pid = 0, $uid = null)
    {

        //Khi đã login true acc, thì truy cập vào admin được không:
        //$this->testLoginTrueAccount();


        //Khi không có quyền, thì trả lại sẽ > 200

        if(!$uid){
            $uid = User::getUserByEmail('admin@abc.com')->id;
        }
        $user = User::find($uid);

        if(!$user)
            dd("testUploadRealFile NOT USER TEST ");


        if($user instanceof User);

        $user->addAllowPermissionRouteNameOnUser('api.member-file.upload');
        $user->addAllowPermissionRouteNameOnUser('api.member-file.update-multi');

        $tk = $user->getUserToken();

        if(!$tk){
            die(" Error, not tk uid = $uid");
        }

        $url = env('APP_URL').'/api/member-file/upload';

//        die("URL = $url");

        $fileName = './tester_glx_upload_auto.'.time();

        $cont = microtime(1);
        file_put_contents($fileName, $cont);

        $cFile = curl_file_create($fileName);

        //$res  = $this->post($url, ['file'=>$cFile, 'set_parent_id'=>0]);
        //$res  = $this->post($url, ['set_parent_id'=>0]);

        $cFile = new \CURLFile($fileName);

        $post = ['set_parent_id' => $pid, 'file_data' => $cFile];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //'Content-Type: application/json',
            'Authorization: Bearer '.$tk,
        ]);
        $result = curl_exec($ch);

        $error_msg = curl_error($ch);
        dump($error_msg);

        curl_close($ch);

        dump('--- STATUS: ');
        dump('RET = '.substr($result, 0, 2000));
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $obj = json_decode($result);

        $this->assertTrue($obj != null);

        //Xóa upload
        //        foreach ($obj->payload AS $objFile)

        //            dump($objFile);

        $id = $obj->payload->id;

        $name = $obj->payload->name;

        dump("\n $id => $name");
        $fid = $id;

        $fileDB = FileUpload::find($fid);

        self::assertTrue($fileDB->file_size == strlen($cont));

        //            self::assertTrue(file_exists($fileDB->file_path));

        dump("File path: $fileDB->file_path");

        if ($delete) {
            if (file_exists($fileDB->file_path)) {
                unlink($fileDB->file_path);
            }
            $fileDB->forceDelete();
        }

        return $id;

    }

    //Todo: cần ktra file quá lớn, và phải bắt được lỗi 413, ko để ra rác
    public function testUploadToBigFile()
    {
        $this->assertTrue(true);
    }

    //Todo: cần ktra Quota của userid, cần thêm 1 hàm tính quota...
    public function testUploadQuota()
    {
        $this->assertTrue(true);
    }

    //Todo: nếu upload 1 file nhiều lần, thì chỉ trả lại file đầu tiên, tránh bị dub DB
    public function testUploadTheSameFileSmall()
    {
        $this->assertTrue(true);
    }
}
