<?php

namespace Tests\Feature;

use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\FolderFile;
use App\Models\Tag;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class FileFolderTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    /**
     * Test multi field autocomplete: đưa tag name vào , thay đổi, save, kiểm tra KQ
     */
    public function testFileFolderUI()
    {
        //Todo: need testFile folder

        $this->testLoginTrueAcc('member@abc.com');

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/member/file');

        $user = User::findUserWithEmail('member@abc.com');
        $user->addAllowPermissionRouteNameOnUser('member.file.index');
        //        route("member.file.index");

        sss(2);

        self::assertTrue(clsTestBase2::findOneByXPath("//div[@class='cls_root_tree']") != null);
        self::assertTrue(clsTestBase2::findOneByXPath("//label[@class='upload_button']") != null);

        self::assertTrue(clsTestBase2::findOneByXPath("//div[@class='upload_zone_glx']") != null);

//        self::assertTrue(clsTestBase2::findOneByXPath("//a[@id='save-all-data']") != null);
        self::assertTrue(clsTestBase2::findOneByXPath("//button[contains(@class,'btn_trash')]") != null);
    }

    /**
     * Ở mebmer/file, Bảng File name: Tạo các file trong DB, sau đó chắc chắn nó sẽ được thấy trên giao diện
     * Tạo thêm 1 file thuộc admin, chắc chắn nó ko thấy trên giao diện
     */
    public function testMemberOnlySeeFileBelongMember()
    {
        $this->testLoginTrueAcc('member@abc.com');
        $uidAdmin = User::findUserWithEmail('admin@abc.com')->id;
        $user = User::where('email', 'member@abc.com')->first();

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/member/file');

        dump(" UID = $user->id");

        //Danh sách các file thuộc member
        $mFile = FileUpload::where('user_id', $user->id)->get();

        $mObj = [];
        //if($mFile->count()< 2)

        $fileNameCreate = 'for_test_only_glx_';
        $fileNameUid1 = $fileNameCreate.'.100';
        $obj = FileUpload::create(['name' => $fileNameCreate.'0', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;
        $obj = FileUpload::create(['name' => $fileNameCreate.'1', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;
        $obj = FileUpload::create(['name' => $fileNameCreate.'2', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;

        dump($obj->toArray());

        //Tạo thêm cái thuộc user1, sau đó chắc chắn nó ko có ở trong index
        $objUid1 = FileUpload::create(['name' => $fileNameUid1, 'user_id' => $uidAdmin]);
        $mObj[] = $objUid1;
        dump($obj->toArray());

        $browser->refresh();
        sss(2);

        $mFile = FileUpload::where('user_id', $user->id)->latest()->get();
        foreach ($mFile as $file) {
            dump(" $file->name ");
            if ($file->id = $objUid1->id) {
                continue;
            }
            $found = clsTestBase2::findOneByXPath("//input[@data-id='$file->id'][contains(@class, 'input_value_to_post')]");
            $this->assertTrue($found != null);
        }

        $notFound = clsTestBase2::findOneByXPath("//input[@data-id='$objUid1->id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($notFound == null);

        //Chuyển sang acc admin, sẽ thấy item
        $this->testLoginTrueAcc('admin@abc.com');
        $browser->visit('/member/file');
        sss(2);
        $check = clsTestBase2::findOneByXPath("//input[@data-id='$objUid1->id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($check != null);

        //Thấy all file trong admin
        $browser->visit('/admin/file');
        sss(2);
        foreach ($mFile as $file) {
            dump(" $file->name ");
            $found = clsTestBase2::findOneByXPath("//input[@data-id='$file->id'][contains(@class, 'input_value_to_post')]");
            $this->assertTrue($found != null);
        }

        sss(2);

        FileUpload::where('name', 'LIKE', $fileNameCreate.'%')->forceDelete();
        //
        //        foreach ($mObj as $item) {
        //            $item->forceDelete();
        //        }

    }

    /**
     * Ở mebmer/file, Cây folder trái: Tạo các folder trong DB, sau đó chắc chắn nó sẽ được thấy trên giao diện
     * Tạo thêm 1 folder thuộc admin, chắc chắn nó ko thấy trên giao diện
     */
    public function testMemberOnlySeeFolderBelongMember()
    {
        $this->testLoginTrueAcc('member@abc.com');

        $user = User::where('email', 'member@abc.com')->first();
        $uidAdmin = User::findUserWithEmail('admin@abc.com')->id;
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/member/file');

        dump(" UID = $user->id");

        //Danh sách các file thuộc member
        $mFile = FolderFile::where('user_id', $user->id)->get();

        $mObj = [];
        //if($mFile->count()< 2)

        $fileNameCreate = 'for_test_only_glx_';
        $fileNameUid1 = $fileNameCreate.'.100';
        $obj = FolderFile::create(['name' => $fileNameCreate.'0', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;
        $obj = FolderFile::create(['name' => $fileNameCreate.'1', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;
        $obj = FolderFile::create(['name' => $fileNameCreate.'2', 'user_id' => $user->id, 'parent_id' => 0]);
        $mObj[] = $obj;

        dump($obj->toArray());

        //Tạo thêm cái thuộc user1, sau đó chắc chắn nó ko có ở trong index
        $objUid1 = FolderFile::create(['name' => $fileNameUid1.'.123', 'user_id' => $uidAdmin, 'parent_id' => 0]);
        $mObj[] = $objUid1;
        dump($obj->toArray());

        $browser->refresh();
        sss(2);

        $mFile = FolderFile::where('user_id', $user->id)->where('parent_id', 0)->latest()->limit(3)->get();
        foreach ($mFile as $file) {
            if ($file->id == $objUid1->id) {
                continue;
            }
            dump(" $file->id, $file->name ");
            $found = clsTestBase2::findOneByXPath("//div[@data-tree-node-id='$file->id'][contains(@class, 'real_node_item')]");
            $this->assertTrue($found != null);
        }

        $check = clsTestBase2::findOneByXPath("//div[@data-tree-node-id='$objUid1->id'][contains(@class, 'real_node_item')]");
        $this->assertTrue($check == null);

        //Chuyển sang acc admin, sẽ thấy item
        $this->testLoginTrueAcc('admin@abc.com');
        $browser->visit('/member/file');
        sss(2);
        dump(" check $objUid1->id ");
        $check = clsTestBase2::findOneByXPath("//div[@data-tree-node-id='$objUid1->id'][contains(@class, 'real_node_item')]");
        $this->assertTrue($check != null);
        sss(1);

        //Thấy all item trong admin
        $browser->visit('/admin/folder-file');
        foreach ($mFile as $file) {
            dump(" $file->name ");
            $found = clsTestBase2::findOneByXPath("//input[@data-id='$file->id'][contains(@class, 'input_value_to_post')]");
            $this->assertTrue($found != null);
        }
        sss(1);

        FolderFile::where('name', 'LIKE', $fileNameCreate.'%')->forceDelete();

        //        foreach ($mObj as $item) {
        //            $item->forceDelete();
        //        }

    }

    /**
     * Login Member, Click UI tạo folder trong root, thấy folder, f5 vẫn thấy, vào acc admin ko thấy, vào vùng Admin Folder thì thấy
     * Xóa đi, F5 ko thấy nữa, admin folder cũng ko còn
     */
    public function testCreateDeleteFolderInRoot()
    {
        //Todo: need testFile folder
        $this->assertTrue(true);
    }

    /**
     * Login Member, Click UI tạo folder trong root, thấy folder, f5 click vào folder child...
     * Xóa đi, F5 ko thấy nữa, admin folder cũng ko còn
     */
    public function testFolderInChildFolderOfRoot()
    {
        //Todo: need testFile folder
        $this->assertTrue(true);
    }

    /**
     * Upload file vào tk user, kiểm tra trong acc user, admin thấy file
     * Xóa file, vào thùng rác thì thấy
     * Xóa vĩnh viễn, vào thùng rác cũng ko thấy nữa
     *
     * @param  int  $pid
     * @param  string  $userEmail
     */
    public function testApiUploadFileInFolderRootDeleteFileInTrash($pid = 0, $userEmail = 'member@abc.com')
    {
        $this->testLoginTrueAcc($userEmail);
        $member = User::where('email', $userEmail)->first();

        if(!$member)
            die(" testApiUploadFileInFolderRootDeleteFileInTrash not member ");

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $xx = new ApiUploadFileTest('');

        $id = $xx->testUploadRealFile(0, $pid, $member->id);

        $fileO = FileUpload::find($id);

        if (strstr(env('APP_URL'), 'localhost')) {
            if (file_exists($fileO->file_path)) {
                @unlink($fileO->file_path);
            }
        }

        dump($fileO->toArray());

        //sss(100);

        //Kiểm tra trên giao diện admin xem có file chưa:
        $this->testLoginTrueAcc(1);
        $browser->visit('/admin/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found != null);

        //Kiểm tra trong acc user:
        $this->testLoginTrueAcc($member->id);
        $browser->visit('/member/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found != null);

        //sẽ ko thấy trên giao diện member của admin user
        $this->testLoginTrueAcc(1);
        $browser->visit('/member/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found == null);

        ////////////////////
        //Xóa đi rồi thì ko thấy nữa
        $fileO->delete();

        //Kiểm tra trên giao diện admin
        $this->testLoginTrueAcc(1);
        $browser->visit('/admin/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found == null);

        //Kiểm tra trong acc user:
        $this->testLoginTrueAcc($member->id);
        $browser->visit('/member/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found == null);

        ////////////////////
        //Nhưng thấy trong in trash:
        $this->testLoginTrueAcc(1);
        $browser->visit('/admin/file?in_trash=1');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found != null);

        //Kiểm tra trong acc user:
        $this->testLoginTrueAcc($member->id);
        $browser->visit('/member/file?in_trash=1');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found != null);

        ////////////////////
        //xóa hẳn rồi thì in trash cũng ko thấy
        $fileO->forceDelete();

        $this->testLoginTrueAcc(1);
        $browser->visit('/admin/file?in_trash=1');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found == null);

        //Kiểm tra trong acc user:
        $this->testLoginTrueAcc($member->id);
        $browser->visit('/member/file?in_trash=1');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$id'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found == null);

    }

    /**
     * Upload file vào 1 folder mới tạo thuộc root, vào folder đó và chắc chắn có file đó hiện ra, và chỉ 1 file trong đó
     * Xóa file vừa upload, và up thêm 1 file khác tại chính url của folder, F5 để thấy file có trong folder hiện tại
     *
     * @param  string  $userEmail
     *
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    public function testUploadFileTrongFolderChild($userEmail = 'member@abc.com')
    {

        $this->testLoginTrueAcc($userEmail);
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $member = User::where('email', $userEmail)->first();

        FolderFile::where('name', 'like', 'test_glx.%')->forceDelete();

        FileUpload::where('name', 'like', 'for_test_only_glx_%')->forceDelete();
        FileUpload::where('name', 'like', 'tester_glx_upload_auto%')->forceDelete();
        FileUpload::where('name', 'like', 'new_name_test_glx_%')->forceDelete();
        FileUpload::where('name', 'like', 'file123_test_upload%')->forceDelete();

        $folderInRoot = FolderFile::create(['user_id' => $member->id, 'parent_id' => 0, 'name' => 'test_glx.'.time()]);

        self::assertTrue($folderInRoot != null);

        $xx = new ApiUploadFileTest('');
        $idFile = $xx->testUploadRealFile(0, $folderInRoot->id, $member->id);
        $fileO = FileUpload::find($idFile);

        if (strstr(env('APP_URL'), 'localhost')) {
            self::assertTrue(file_exists($fileO->file_path));
        }

        //xoa luon
        @unlink($fileO->file_path);

        dump($fileO->toArray());

        //        sleep(100);

        sleep(2);

        //Vào member, chắc chắn sẽ có file đó:
        //Kiểm tra trong acc user:
        $this->testLoginTrueAcc($member->id);
        $browser->visit('/member/file');
        $found = clsTestBase2::findOneByXPath("//input[@data-id='$idFile'][contains(@class, 'input_value_to_post')]");
        $this->assertTrue($found != null);

        //Click vào folder name để vào folder, sẽ thấy file đó:
        //Folder file Kieeur moi khong thay phan tu nay: 5.9.24
        clsTestBase2::findOneByXPath("//div[@data-tree-node-id='$folderInRoot->id']/span[contains(@class,'node_name')]")->click();


        $pr = clsTestBase2::findOneByXPath("//input[contains(@class, 'search_top_grid')][@data-autocomplete-id='filter_field_parent_id']");

        dump('VAL PID = '.$pr->getAttribute('value'));

        self::assertTrue($pr->getAttribute('value') == $folderInRoot->id);

        $objMeta = FileUpload::getMetaObj();

        dump($drv->getCurrentURL());
        $ss = DEF_PREFIX_SEARCH_URL_PARAM_GLX.$objMeta->getSNameFromField('parent_id').'='.$folderInRoot->id;

        dump($ss);

        self::assertStringContainsString($ss, $drv->getCurrentURL());

        //Chắc chắn chỉ có 1 file trong folder này:
        $mm = $drv->findElements(WebDriverBy::xpath("//input[@data-field='id'][contains(@class,'input_value_to_post')]"));
        self::assertTrue(count($mm) == 1);
        self::assertTrue($mm[0]->getAttribute('data-id') == $idFile);

        $fileO->forceDelete();

        sleep(1);
        //Up tiếp 1 file tại chính url này:
        $xx = new ApiUploadFileTest('');
        $idFile = $xx->testUploadRealFile(0, $folderInRoot->id, $member->id);
        $fileO = FileUpload::find($idFile);

        if (strstr(env('APP_URL'), 'localhost')) {
            self::assertTrue(file_exists($fileO->file_path));
        }
        //xoa luon
        @unlink($fileO->file_path);
        $browser->refresh();

        sleep(2);

        //Chắc chắn chỉ có 1 file trong folder này:
        $mm = $drv->findElements(WebDriverBy::xpath("//input[@data-field='id'][contains(@class,'input_value_to_post')]"));
        self::assertTrue(count($mm) == 1);
        self::assertTrue($mm[0]->getAttribute('data-id') == $idFile);
        $fileO->forceDelete();

        //Kết thúc xóa Dir
        $folderInRoot->forceDelete();

    }

    public function testMoveSomeFileToChildFolder()
    {
        //Todo:
        $this->assertTrue(true);
    }

    public function testLimitQuotaUpload()
    {
        //Todo:
        $this->assertTrue(true);
    }

    public function testLimitQuotaDownload()
    {
        //Todo:
        $this->assertTrue(true);
    }

    /**
     * Test upload file trên giao diện web
     * Click nút upload ở /member/file, refresh để xem file tồn tại
     * Tạo folder, đi vào folder đó, rồi upload file vào, refresh xem file thuộc folder đó
     */
    public function testUploadFileInUi()
    {

        $userEmail = 'member@abc.com';
        $this->testLoginTrueAcc($userEmail);
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $browser->visit('/member/file');

        //        $member = User::where("email", $userEmail)->first();
        //        $folderInRoot = FolderFile::create(['user_id' => $member->id, 'parent_id'=> 0, 'name'=>'test_glx.'.time()]);
        //
        //        self::assertTrue($folderInRoot != null);

        $obj = clsTestBase2::findOneById('drop-area-upload1_file');

        $filePath = sys_get_temp_dir().'/file123_test_upload_cms.'.time();
        file_put_contents($filePath, time());

        $obj->sendKeys($filePath);
        sss(3);
        $browser->refresh();
        sss(2);
        @unlink($filePath);

        $obj = clsTestBase2::findOneByXPath("//input[@data-field='name'][contains(@class,'input_value_to_post')]");
        self::assertTrue($obj != null);
        self::assertTrue($obj->getAttribute('value') == basename($filePath));
        $id = $obj->getAttribute('data-id');

        dump("\n xxx ID = $id");
        //        sss(1111);

        $file = FileUpload::find($id);
        $fcl = FileCloud::find($file->cloud_id);

        @unlink($fcl->file_path);

        $file->forceDelete();
        $fcl->forceDelete();

        //Tạo folder, đi vào folder đó, rồi upload file vào, refresh xem file thuộc folder đó
        $folderName = 'folder_for_testing.'.time();
        $foldObj = FolderFile::create(['name' => $folderName, 'user_id' => auth()->id(), 'parent_id' => 0]);

        $meta = FileUpload::getMetaObj();

        $skey = $meta->getSearchKeyField('parent_id');
        $url = "/member/file?$skey=$foldObj->id";
        $browser->visit($url);

        $obj = clsTestBase2::findOneById('drop-area-upload1_file');

        $filePath = sys_get_temp_dir().'/file123_test_upload_2_cms.'.microtime(1);
        file_put_contents($filePath, time());

        $obj->sendKeys($filePath);
        sss(3);
        $browser->refresh();
        sss(2);
        @unlink($filePath);

        $obj = clsTestBase2::findOneByXPath("//input[@data-field='name'][contains(@class,'input_value_to_post')]");
        self::assertTrue($obj != null);
        self::assertTrue($obj->getAttribute('value') == basename($filePath));
        $id = $obj->getAttribute('data-id');

        $file = FileUpload::find($id);
        $fcl = FileCloud::find($file->cloud_id);
        @unlink($fcl->file_path);
        $file->forceDelete();
        $fcl->forceDelete();
        $foldObj->forceDelete();
    }
}
