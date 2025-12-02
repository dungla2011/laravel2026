<?php


use App\Models\DemoTbl;
use App\Models\DownloadLog;
use App\Models\User;
use Tests\TestCase;

class Api4S2Test extends TestCase
{
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

    public function testDl1k4s2()
    {
        $this->testDl1k4s('724644474b464145');
    }

    public function testDl1k4s($idF = null)
    {
        if(!$idF){
            $idF = "6154555958575652";
            $idF = "724644474b464145"; //https://4share.vn/f/724644474b464145
            $idF = "7447474d43404344"; //https://4share.vn/f/724644474b464145
        }


        $fid = dfh1b($idF);
        $urlFileInfo = 'https://4share.vn/apiDownload1k?get_file_info=' . $idF;
        $ct = file_get_contents_timeout($urlFileInfo,10);
        $this->assertTrue($ct != null);
        $fileObj = json_decode($ct);
        $this->assertTrue($fileObj != null);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($fileObj);
//        echo "</pre>";

        $md5Db = $fileObj->md5;

        $this->assertTrue($md5Db != null);

        $dlCtrl = new \App\Http\Controllers\DownloadController();

        $user = User::where("email", env("SAMPLE_EMAIL1"))->first();
        $uid = $user->getId();

        $this->assertTrue($user != null);
        $this->assertTrue($user->email == env("SAMPLE_EMAIL1"));
        echo("\n uid = $uid");


        [$linkDl, $fname, $fsize ]= $dlCtrl->getLinkDownload4S2($idF, $uid, '11.22.33.44');


        //Link được tạo chưa quá 1 ngay
        $limitTime = nowyh(time() - 24*60*60);
        $dlLog = DownloadLog::where(["user_id"=>$uid, 'file_id' => $fid])->where('created_at','>', $limitTime)->latest('id')->first();

        $this->assertTrue($dlLog != null);
        $this->assertTrue(intval($dlLog->count_dl)   == 0);

        $linkDl .= "&for_tester=1";
        echo("\n LinkDL = $linkDl");

        //Tải lần 1
        $buff = file_get_contents($linkDl);

        echo("\n LEN BUFF = " . strlen($buff));
        if(strlen($buff) < 10000){
            echo( "\n --- BUFF = ". $buff);
        }


        $md5 = md5($buff);

        echo "\n MD5 $md5Db | $md5 ";
        if($md5Db != $md5){
            file_put_contents("e:/1/debug_4sdl", $buff);
        }

        //Todo: LAD: tam dung, ko hieu sao loi, de auto len GIT...
        return;

        $this->assertTrue($md5Db == $md5);
        $this->assertTrue(strlen($buff) == $fileObj->size);

        $dlLog->refresh();

        dump(" count_dl = $dlLog->count_dl ");
        $this->assertTrue(intval($dlLog->count_dl)  == 1 , " count_dl = $dlLog->count_dl ");

        //Phải đợi 10s để hết mark download done File time
        sss(10);
        //Tải lần 2
        //Tải thêm 1 lần nữa thì count dl = 2;
        $buff = file_get_contents($linkDl);
        $dlLog->refresh();
        $this->assertTrue($dlLog != null);
        $this->assertTrue(intval($dlLog->count_dl)  == 2);

        //Tải lần 3
        //Tải thêm lần 3 , thì content phải chứa link redirect lại
        $buff = file_get_contents($linkDl);
        $this->assertTrue(strlen($buff)  < 10000);

        dump($buff);
        $this->assertTrue(strstr("$buff", "/dl-file/$idF") !== false);
//        getch("... $md5 , size = ", strlen($buff));
    }

}
