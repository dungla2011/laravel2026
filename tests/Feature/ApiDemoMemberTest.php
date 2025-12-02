<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\User;
use Tests\TestCase;

define('DEF_GID_MEMBER_TEST', 3);

class ApiDemoMemberTest extends TestCase1
{
    public function assertDataApi($ret)
    {

//        dump($ret);


        $this->assertTrue(isset($ret['payload']));
        //        $this->assertTrue(isset($ret['message']));
        $this->assertTrue(isset($ret['code']));

        //        $this->assertTrue(isset($dataAll['data']));
        //        $this->assertTrue(isset($dataAll['current_page']));
        //        $this->assertTrue(isset($dataAll['total']));
    }

    protected function setUp(): void
    {
        parent::setUp();
        // set your headers here
        $user = User::where('email', 'member@abc.com')->first();

        $this->assertTrue(! empty($user));

        $role1 = $user->_roles->toArray();

        //Chắc chắn là đã có 1 acc member, nếu ko thì tạo nó trước khi test này
        //user test_member1, role 3
        //Chỉ có 1 role và roleid = 3 (member)
        self::assertTrue(count($role1) == 1);
        self::assertTrue($role1[0]['id'] == DEF_GID_MEMBER_TEST);

        dump('Role-user: '.$user->getRoleIdUser());

        $tk = $user->getUserToken();
        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);
    }

    /**
     * Với GID, Get Index một trường không được quyền show trên index, kết quả API trả lại sẽ ko thấy trường đó
     * Sau đó enable trường đó, thì Api lại thấy
     */
    public function testEnableDisableSeeInIndexWithRoleId($field = 'textarea1', $roleId = DEF_GID_MEMBER_TEST)
    {

        //Todo:
        $objMeta = DemoTbl::getMetaObj();
        $objMeta->setAllowGidOnIndexField([$field], $roleId, 1);
        $objMeta->deleteClearCacheMetaApi_();

        $user = User::where('email', 'member@abc.com')->first();
        if ($user instanceof User);

        $rname = 'api.member-demo.list';
        $url = route($rname);

        //Remove quyền thì phải ko vào được
        $user->removePermissionRouteNameOnUser($rname);
//        sss(100);

        $response = $this->getJson($url);
        self::assertTrue($response->getStatusCode() != 200, ' *** RETURN Status: '.$response->getStatusCode());

        //Add lại quyền thì phải vào được
        $user->addAllowPermissionRouteNameOnUser($rname);
        sss(1);

        $response = $this->getCurl1($url . "?limit=3", $user->getUserToken());
        self::assertTrue($response->status() == 200, $url . ' / *** RETURN Status: '.$response->status());


//        dump("CONT " . $response->getContent());

        $ret = json_decode($response->getContent(), true);
        dump($ret);

        $this->assertDataApi($ret);

        $dataAll = $ret['payload'];

        dump('Total = '.$dataAll['total']);
        dump('per_page = '.$dataAll['per_page']);

        $data = $dataAll['data'];
        foreach ($data as $m1) {
            //            dump($m1);
        }

        $objMeta = DemoTbl::getMetaObj();

        //Tất cả các field được show in Index:
        $mIndexField = $objMeta->getShowIndexAllowFieldList($roleId);

        //        dump($data);

        foreach ($data as $oneDemo) {
            $mFieldApiRet = array_keys($oneDemo);

            //so sánh 2 mfile phải bằng nhau
            $diff = array_diff($mIndexField, $mFieldApiRet);
            self::assertTrue(! $diff, ' 2 mảng phải bằng nhau');
        }

        //        dump($mIndexField);
        self::assertTrue(in_array($field, $mIndexField));

        ///////////////////////////////////////////////////////
        //Disable 1 field, thì API sẽ trả lại nhiều hơn 1 field
        $objMeta->setAllowGidOnIndexField([$field], $roleId, 0);
        $objMeta->deleteClearCacheMetaApi_();

        $mIndexField = $objMeta->getShowIndexAllowFieldList($roleId);

        //
        //        dump($mIndexField);

        self::assertTrue(! in_array($field, $mIndexField));

        //Query lại để chắc là API ko còn textarea1
        $url = route($rname);
        $response = $this->getCurl1($url, $user->getUserToken());
        $ret = json_decode($response->getContent(),1);// $response->decodeResponseJson();
        $this->assertDataApi($ret);
        $dataAll = $ret['payload'];
        $data = $dataAll['data'];
        foreach ($data as $oneDemo) {
            $mFieldApiRet = array_keys($oneDemo);
            self::assertTrue(! in_array($field, $mFieldApiRet));
        }

        ///////////////////////////////////////////////////////
        //Enable 1 field, thì API sẽ trả lại nhiều hơn 1 field
        $objMeta->setAllowGidOnIndexField([$field], $roleId, 1);
        $objMeta->deleteClearCacheMetaApi_();
        $mIndexField = $objMeta->getShowIndexAllowFieldList($roleId);
        self::assertTrue(in_array($field, $mIndexField));

        //Query lại để chắc là API lại thấy còn textarea1
        $url = route($rname);
        $response = $this->getCurl1($url);
        $ret = json_decode($response->getContent(),1);// $response->decodeResponseJson();
        $this->assertDataApi($ret);
        $dataAll = $ret['payload'];
        $data = $dataAll['data'];
        foreach ($data as $oneDemo) {
            $mFieldApiRet = array_keys($oneDemo);
            self::assertTrue(in_array($field, $mFieldApiRet));
        }

    }

    //Todo: nếu ko có quyền edit trên index, thì API gửi lên sẽ báo lỗi ko có quyền edit
    public function testEnableDisableEditInIndexWithRoleId($field = 'textarea1', $roleId = DEF_GID_MEMBER_TEST)
    {

    }

    //Todo: nếu ko có quyền edit trên GetOne, thì API gửi lên sẽ báo lỗi ko có quyền edit
    public function testEnableDisableEditInGetOneWithRoleId($field = 'textarea1', $roleId = DEF_GID_MEMBER_TEST)
    {

    }
}
