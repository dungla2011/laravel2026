<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\User;
use Tests\TestCase;

class ApiDemoAdminAddEditTest extends TestCase1
{
    protected function setUp(): void
    {
        parent::setUp();
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

    public function assertDataApi($ret)
    {

        dump($ret);

        $this->assertTrue(isset($ret['payload']));
        //        $this->assertTrue(isset($ret['message']));
        $this->assertTrue(isset($ret['code']));

        //        $this->assertTrue(isset($dataAll['data']));
        //        $this->assertTrue(isset($dataAll['current_page']));
        //        $this->assertTrue(isset($dataAll['total']));
    }

    /**
     * Thay đổi Disable/Enable của Editable Role của 1 Field, với 1 GID, và post vào đó để có kết quả không được phép, và thành công
     */
    public function testAdminDemoFieldEditable($gid = 3)
    {

        $demo = DemoTbl::latest()->first();
        $url = '/api/demo/update/'.$demo->id;
        if ($gid == 3) {

            $user = User::where('email', 'member@abc.com')->first();
            dump("UID = $user->id ");

            if (! $demo = DemoTbl::where('user_id', $user->id)->first()) {
                $demo = DemoTbl::create(['user_id' => $user->id]);
            }

            $tk = $user->getUserToken();

            $user->addAllowPermissionRouteNameOnUser('api.member-demo.update');

            dump("Token : $tk");
            //$this->withToken("123456")->getJson(route("api.demo.list"));
            $this->withHeader('Authorization', 'Bearer '.$tk);
            $url = '/api/member-demo/update/'.$demo->id;
        }

        $objMeta = DemoTbl::getMetaObj();
        //        $objMeta->setAllowGidOnIndexField(['id', 'name'], 3, 1);

        self::assertTrue($objMeta->isShowIndexField('id', $gid) != '');
        self::assertTrue($objMeta->isShowIndexField('textarea2', $gid) != '');

        //        self::assertTrue($objMeta->isShowIndexField('textarea2', 1) <= 0);
        //        self::assertTrue($objMeta->isEditableField('textarea2', 1) <= 0);

        $objMeta->setAllowGidOnIndexField(['textarea2'], $gid, 1);

        $setVal = microtime(1);
        //        $setVal = "1234";

        $ret = @file_get_contents(env("APP_URL")."/tool/gw/delete_cache_meta.php?table=demo_tbls");
        
        dump("Delete cache meta ret: $ret");

        //Bỏ quyền edit field
        $objMeta->setAllowGidEditIndexField(['textarea2'], $gid, 0);
        $objMeta->setAllowGidEditGetOneField(['textarea2'], $gid, 0);

        //        sss(20);

        //        $demo = DemoTbl::find(565);
        //        $this->withHeader('Authorization' ,'Bearer xxx');

        $res = $this->postCurl1($url, ['textarea2' => $setVal]);
        //Khi không có quyền, thì trả lại sẽ > 200

        dump("Status HTTP:" . $res->status()); 
        dump("GID = $gid , CONT ". substr($res->getContent(), 0 ,500));

        self::assertTrue($res->status() != 200, ' status = '.$res->status());

        $ret = @file_get_contents(env("APP_URL")."/tool/gw/delete_cache_meta.php?table=demo_tbls");
        dump("Delete cache meta ret: $ret");
        //Cấp lại quyền edit field
        $objMeta->setAllowGidEditIndexField(['textarea2'], $gid, 1);
        $objMeta->setAllowGidEditGetOneField(['textarea2'], $gid, 1);

        //        sss(1);

        //        $url = "/api/demo/update/".$demo->id;
        $res = $this->postCurl1($url, ['textarea2' => "$setVal"]);
        dump($res->status()." / $demo->id / ".$setVal);
        //        dump(substr($response->getContent(),0,500));
        //        dump($res->dumpHeaders());

        self::assertTrue($res->status() == 200, $res->status());

        //Xem đã được sửa đúng chưa
        $demo = DemoTbl::find($demo->id);
        dump($demo->toArray());
        self::assertTrue("$demo->textarea2" == "$setVal", " $demo->textarea2 == $setVal ");
    }

    public function testEditAbleWithMember()
    {
        $this->testAdminDemoFieldEditable(3);
    }

    /**
     * Kiểm tra insert update validate rule demo, name <, > 20 ký tự
     * @return void
     */
    public function testInsertUpdateValidateRuleDemo()
    {

        $user = User::where('email', env('AUTO_SET_ADMIN_EMAIL'))->first();
        dump("UID = $user->id ");

        $nameLong = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890';

        $demo = DemoTbl::create(['name' => $nameLong]);
        dump("DEMO ID = $demo->id ");
//        return;
        $tk = $user->getUserToken();

        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);
        $url = '/api/demo/update/'.$demo->id;
        $res = $this->postCurl1($url, ['name' => 'abc-' . $nameLong]);
        //Khi không có quyền, thì trả lại sẽ > 200

        dump("CONTx : ", $ret = $res->getContent());

        //Chắc chắn là có báo lỗi quá 20 ký tự ở name
        $rt = json_decode($ret);
        self::assertTrue($rt->code == -1, ' code = '.$rt->code);
        self::assertTrue(strstr($rt->message, "50") !== false, ' message = '.$rt->message);
        self::assertTrue($res->status() == 400, ' status = '.$res->status());

        $res = $this->postCurl1($url, ['name' => 'abc123qqq']);
        self::assertTrue($res->status() == 200, ' status = '.$res->status());

        $demo->forceDelete();

        //Thêm quá dài hơn 20kt
        $url = '/api/demo/add';
        $res = $this->postCurl1($url, ['name' => 'abc-' . $nameLong]);
        self::assertTrue($rt->code == -1, ' code = '.$rt->code);
        self::assertTrue(strstr($rt->message, "50") !== false, ' message = '.$rt->message);
        self::assertTrue($res->status() == 400, ' status = '.$res->status());

        //Thêm đúng
        $url = '/api/demo/add';
        $res = $this->postCurl1($url, ['name' => 'abc']);
        dump($ret = $res->getContent());
        $rt = json_decode($ret);
        self::assertTrue($rt->code == 1, ' code = '.$rt->code);
        self::assertTrue($res->status() == 200, ' status = '.$res->status());

        dump("DemoTbl::latest()->first() id: " . DemoTbl::latest()->first()->id);

        DemoTbl::latest()->first()->forceDelete();

    }

    //todo: test add, edit, get đặc biệt với extra field, là gì nhỉ?

}
