<?php

namespace Tests\Feature;

use App\Models\FileUpload;
use App\Models\FolderFile;
use App\Models\User;
use Faker\Core\File;
use Illuminate\Testing\TestResponse;

class ApiFolderFileTest extends TestCase1
{
    /**
     * @var TestResponse
     */
    protected function setUp(): void
    {
        parent::setUp();
        // set your headers here
        if (!User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
        if (!User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
$tk = User::where("email", 'admin@abc.com')->first()->getUserToken();
        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);
    }

    /**
     * Upload 2 file random name, di chuyển vào 1 folder mới tạo, kiểm tra parent trong db
     * Move 2 file ra root, kiểm tra parent = 0 trong db
     */
    public function testApiMoveFile()
    {

        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" testApiListFile not member ");

        dump("<br/>\n upload 001");
        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);
        self::assertTrue($fileO !== null);
        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }
        dump("<br/>\n upload 002");
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $file1 = FileUpload::find($idFile);
        self::assertTrue($file1 !== null);
        if (file_exists($file1->file_path)) {
            unlink($file1->file_path);
        }

        $folderInRoot = FolderFile::create(['user_id' => $member->id, 'parent_id' => 0, 'name' => 'test_glx.'.time()]);

        ///api/member-file/update-multi
        //id_list: ,95,60
        //move_to_parent_id: 1

        dump($folderInRoot);

        //        sss(111);

        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);

        $url = '/api/member-file/update-multi';

        $res = $this->postCurl1($url, ['id_list' => "$file1->id,$fileO->id", 'move_to_parent_id' => $folderInRoot->id]);
        dump($res->status().' / / ');
        dump($res->getContent());
        sss(1);
        self::assertTrue($res->status() == 200);
        $fileO = FileUpload::find($fileO->id);
        $file1 = FileUpload::find($file1->id);

        echo "<br/>\nCP: $fileO->parent_id / $folderInRoot->id";

        self::assertTrue($fileO->parent_id == $folderInRoot->id);
        self::assertTrue($file1->parent_id == $folderInRoot->id);

        //Move về root
        $res = $this->postCurl1($url, ['id_list' => "$fileO->id", 'move_to_parent_id' => 0]);
        dump($res->status().' / / ');
        dump($res->getContent());
        self::assertTrue($res->status() == 200);
        $fileO = FileUpload::find($fileO->id);
        self::assertTrue($fileO->parent_id == 0);

        $folderInRoot->forceDelete();
        $fileO->forceDelete();
        $file1->forceDelete();

    }

    /**
     * Upload file thuộc user, và 1 folder ko thuộc user
     * Move file vào folder đó để thấy báo lỗi
     */
    public function testApiMoveFileToWrongUser()
    {

        $uidAdmin = User::findUserWithEmail('admin@abc.com')->id;
        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" Not member testApiMoveFileToWrongUser");
        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');
        dump("<br/>\n upload 003");
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);
        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }

        //Tạo folder ở user admin, để member move vào xem có báo lỗi ko
        $folderInRoot = FolderFile::create(['user_id' => $uidAdmin, 'parent_id' => 0, 'name' => 'test_glx.'.time()]);

        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);

        $url = '/api/member-file/update-multi';

        $res = $this->postCurl1($url, ['id_list' => "$fileO->id", 'move_to_parent_id' => $folderInRoot->id]);
        dump($res->status().' / / Move to ID: '.$folderInRoot->id);
        dump($res->getContent());

        $folderInRoot->forceDelete();
        $fileO->forceDelete();

        //        $file1->forceDelete();
        self::assertTrue($res->status() == 400);

    }

    /**
     * API list file user, chỉ hiển thị các file thuộc user
     * và các trường có trong API list, không thừa thiếu
     */
    public function testApiListFile()
    {

        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" testApiListFile not member ");
        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');

        dump("<br/>\n upload 004");
        //Upload 2 file, 1 file thuộc user, 1 file ko thuộc
        //Sau đó list api, sẽ thấy file thuộc user, ko thấy file kia
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);
        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }
        dump("<br/>\n upload 005");
        $admin = User::findUserWithEmail('admin@abc.com');
        if(!$admin)
            die(" testApiDeleteFileNotBelongUser not member ");
        $idFile = $xx->testUploadRealFile(0, 0, $admin->id);
        $file1 = FileUpload::find($idFile);
        if (file_exists($file1->file_path)) {
            unlink($file1->file_path);
        }

        $mmFile = FolderFile::where(['user_id' => $member->id]);

        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));

//        $this->withHeader('Authorization', 'Bearer '.$tk);
//
//        $url = '/api/member-file/list';
//
//        $res = $this->get($url);
//
//        dump($res->status().' / / ');
//
//// Check if the response status is 403
//        if ($res->status() == 403) {
//            dump('Access denied: 403 Forbidden');
//        } else {
//            dump('Access granted: '.$res->status());
//        }
//
//        dump($res->status().' / / ');
//        //        dump($res->getContent());
//
//        self::assertTrue($res->status() == 200);

        $member->addAllowPermissionRouteNameOnUser('api.member-file.list');

        $url = env('APP_URL').'/api/member-file/list';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$tk,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        self::assertTrue($httpCode == 200);

        $m1 = json_decode($response);

        $m2 = $m1->payload->data;

        $objMeta = FileUpload::getMetaObj();

        echo "\n <br>: table_name_model: " . $objMeta->table_name_model;
        echo "<br/>\n DB: ". $objMeta::$_dbConnection->query('select database()')->fetchColumn();

        $mFileShowIndex = $objMeta->getShowIndexAllowFieldList($member->getRoleIdUser());



        dump($mFileShowIndex);
//        die(sys_get_temp_dir());

        $mIdAll = [];
        foreach ($m2 as $fileDb) {
            dump($fileDb->id);
            $mIdAll[] = $fileDb->id;
            $mVal = array_keys(get_object_vars($fileDb));

            //Bỏ các trường mở rộng thông tin
            $mVal = array_filter($mVal, function ($e) {
                if ($e[0] != '_') {
                    return $e;
                }
            });

            $dif = array_diff($mVal, $mFileShowIndex);

            if ($dif) {

                dump("--- In Meta:");
                dump($mFileShowIndex);
                dump("--- In DB:");
                dump($mVal);

            }

            //chắc chắn các trường hiển thị ko thừa thiếu
            dump($dif);
            self::assertTrue(empty($dif));
        }

        dump($mFileShowIndex);

        self::assertTrue(in_array($fileO->id, $mIdAll));
        self::assertTrue(! in_array($file1->id, $mIdAll));

        //        dump($m2);
        //        $folderInRoot->forceDelete();
        $fileO->forceDelete();
        $file1->forceDelete();

    }

    /**
     * API đổi tên 1 file, kiểm tra db
     * Thay đổi 1 trường ko được quyền edit, như filesize xem có báo lỗi
     * Cho phép trường đó được edit, thay đổi, xem ok không
     * Và lại disable trường đó, check để ko update được
     */
    public function testApiUpdateFileFields()
    {

        $objMeta = FileUpload::getMetaObj();
        $mMeta = FileUpload::getApiMetaArray();
        //$fieldList = array_keys($mMeta);

        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" testApiDeleteFileNotBelongUser not member ");

        $member->addAllowPermissionRouteNameOnUser('api.member-file.update');

        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');
        dump("<br/>\n upload 006");
        //Upload 1 file
        //Sau đó api rename file, và thay đổi 1 trường ko được edit
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);
        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }

        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);

        $url = "/api/member-file/update/$fileO->id";
        $newName = 'new_name_test_glx_'.time();

        $res = $this->postCurl1($url, ['name' => $newName]);

        dump($res->status().' / / ');
        dump($res->getContent());

        $fileO = FileUpload::find($idFile);
        self::assertTrue($fileO->name == $newName);

        $logUpdate = 'test_update_log_'.microtime(1);

        $objMeta->setAllowGidEditGetOneField('log', $member->getRoleIdUser(), 0);


        //Tìm 1 trường ko thể editable, thử edit trường đó
        $url = "/api/member-file/update/$fileO->id";
        $res = $this->postCurl1($url, ['log' => $logUpdate]);
        dump($res->status().' / / ');
        self::assertTrue($res->status() == 400);
        $fileO = FileUpload::find($idFile);

        self::assertTrue($fileO->log != $logUpdate);

        //Đổi quyền edit, để update được
        $objMeta->setAllowGidEditGetOneField('log', $member->getRoleIdUser(), 1);
        sleep(1);
        $url = "/api/member-file/update/$fileO->id";
        $res = $this->postCurl1($url, ['name' => $newName, 'log' => $logUpdate]);
        dump($res->getContent());

        dump($res->status().' / / ');
        self::assertTrue($res->status() == 200);
        $fileO = FileUpload::find($idFile);
        self::assertTrue($fileO->log == $logUpdate);

        //Đổi quyền edit, lại ko update được
        $objMeta->setAllowGidEditGetOneField('log', $member->getRoleIdUser(), 0);

        $url = "/api/member-file/update/$fileO->id";
        $res = $this->postCurl1($url, ['name' => $newName, 'log' => $logUpdate]);
        dump($res->status().' / / ');
        dump($res->getContent());
        self::assertTrue($res->status() == 400);

        //Validate không nó name thì ko update được
        $res = $this->postCurl1($url, ['log' => $logUpdate]);
        dump($res->status().' / / ');
        dump($res->getContent());
        self::assertTrue($res->status() == 400);

        $fileO->forceDelete();

    }

    /**
     * Tạo 1 file trong db
     * login as userid, check api get file
     * đổi userid khác, api ko get được file
     */
    public function testApiGetFileNotBelongUser()
    {

        $objMeta = FileUpload::getMetaObj();
        $mMeta = FileUpload::getApiMetaArray();
        //$fieldList = array_keys($mMeta);

        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" testApiDeleteFileNotBelongUser not member ");
        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');

        dump("<br/>\n upload 007");
        //Upload 1 file
        //Sau đó api rename file, và thay đổi 1 trường ko được edit
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);

        self::assertTrue($fileO != null);

        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }

        $member->addAllowPermissionRouteNameOnUser('api.member-file.get');

        $this->withHeader('Authorization', 'Bearer '.$tk);
        $url = "/api/member-file/get/$fileO->id";
        dump("URL = $url, tk = $tk, $fileO->id");
//        $res = $this->get($url);
        $res = $this->getCurl1($url, $tk);

        dump($res->status().' / / ');
        dump(substr($res->getContent(), 0, 300));

        self::assertTrue($res->status() == 200);
        //

        //Đổi userid khác, sẽ ko get được:
        $this->refreshApplication();
        if (!$us = User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
        if (!$us = User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
        $tk = $us->getUserToken();

        $this->withHeader('Authorization', 'Bearer '.$tk);
        $url = "/api/member-file/get/$fileO->id";
        dump("URL = $url, tk = $tk, FID: $fileO->id");
        $res = $this->get($url);
        dump(substr($res->getContent(), 0, 500));
        dump(' Status = '.$res->status());

        //Todo: Tai sao admin van truy capj duoc vao cua usser??? - Da fix
        self::assertTrue($res->status() == 400);

        //        return;

        //Cần thêm refresh này để xóa sesion cũ:
        $this->refreshApplication();
        //Nếu đổi sang UID thì lại get ok

        //        $fileO->deleted_at = null;
        //        $fileO->update();
        FileUpload::withTrashed()->find($fileO->id)->restore();

        $fileO = FileUpload::find($fileO->id);
        $fileO->user_id = $us->id;
        $fileO->update();

        $this->withHeader('Authorization', 'Bearer '.$tk);
        //        $url = "/api/member-file/get/$fileO->id";
        dump("URL = $url, tk = $tk");
        $res = $this->get($url);
        dump(' Status = '.$res->status());
        self::assertTrue($res->status() == 200);

        $fileO->forceDelete();
    }

    /**
     * Check Delete file của user ok
     * chuyển sang user khác để delete file đó, sẽ ra báo lỗi 400
     */
    public function testApiDeleteFileNotBelongUser()
    {
        $objMeta = FileUpload::getMetaObj();
        $mMeta = FileUpload::getApiMetaArray();
        //$fieldList = array_keys($mMeta);

        $member = User::findUserWithEmail('member@abc.com');
        if(!$member)
            die(" testApiDeleteFileNotBelongUser not member ");

        $member->addAllowPermissionRouteNameOnUser('api.member-file.delete');
        $tk = $member->getUserToken();
        $xx = new ApiUploadFileTest('');

        dump("<br/>\n upload 008");
        //Upload 1 file
        //Sau đó api rename file, và thay đổi 1 trường ko được edit
        $idFile = $xx->testUploadRealFile(0, 0, $member->id);
        $fileO = FileUpload::find($idFile);

        self::assertTrue($fileO != null);

        if (file_exists($fileO->file_path)) {
            unlink($fileO->file_path);
        }

        $this->withHeader('Authorization', 'Bearer '.$tk);
        $url = "/api/member-file/delete?id=$fileO->id";
        dump("URL = $url, tk = $tk");
        $res = $this->getCurl1($url, $tk);

        dump($res->status().' / / ');
        dump(substr($res->getContent(), 0, 300));

        self::assertTrue($res->status() == 200);

        //Phục hồi lại file
        FileUpload::withTrashed()->find($idFile)->restore();
        //        $fileO->deleted_at = null;
        //        $fileO->update();

        dump(" idFILE = $idFile");
        //
        dump('login admin ...........');
        //Đổi userid khác, sẽ ko delete được:
        if (!User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
        if (!User::where('email', 'admin@abc.com')->first()) {
            User::createUserAdminDefault();
        }
$tk = User::where("email", 'admin@abc.com')->first()->getUserToken();
        //Phuc hoi lai file do:
        //        $fileO->user_id = 1;

        $this->refreshApplication();
        $this->withHeader('Authorization', 'Bearer '.$tk);
        $url = "/api/member-file/delete?id=$idFile";

        $res = $this->getCurl1($url, $tk);

        dump($res->status()." / / $url / $tk");
        dump(substr($res->getContent(), 0, 300));

        self::assertStringContainsString('Not your item to dele', $res->getContent());

        self::assertTrue($res->status() == 400);

        $fileO->forceDelete();

    }
}
