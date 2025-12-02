<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\User;
use Tests\TestCase;

class ApiDemoGetDataTest extends TestCase
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
     * kiểm tra API có trả lại các trường mở rộng với field bắt đầu bằng _
     * Xem index api trả lại các trường extra đúng như được khai báo, cấp quyền không:
     */
    public function testGetIndexApiDemo($gid = 1)
    {

        $demo = DemoTbl::latest()->first();

        //api.demo.list
        //api.demo.get

        if ($gid == 1) {
            $user = User::getUserByEmail('admin@abc.com');
        }
        if ($gid == 3) {
            $user = User::getUserByEmail('member@abc.com');
        }

        // Assert user found
        $this->assertNotNull($user, "User not found for gid $gid");

        $tk = $user->getUserToken();

        $linkApi = route('api.demo.list');
        dump("TKx: $tk / ". route('api.demo.list'));

        $user->addAllowPermissionRouteNameOnUser('api.demo.list');

        $response = $this->withToken("$tk")->getJson(route('api.demo.list'));

//        $this->assertEquals(200, $response->getStatusCode());

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $linkApi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $tk
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->assertEquals(200, $httpcode);

        curl_close($curl);

//        echo "<br/>\n RET = $response ";
        $user->removePermissionRouteNameOnUser('api.demo.list');

//        $ret = $response->decodeResponseJson();

        $ret = json_decode($response, true);

        //        dump($ret);

        self::assertTrue($ret['payload']['data'] != null);

        //        dump($ret['payload']);

        $data = $ret['payload']['data'];

        $meta = DemoTbl::getMetaObj();
        $mField = $meta->getShowIndexAllowFieldList($gid);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r(array_values($mField));
        //        echo "</pre>";

//        foreach ($data as $one)
        $one = $data[0];
        {
            //            dump($one);
            //Một item sẽ có các trường như Meta
            //item sẽ có các trường tương ứng các hàm data extra của Meta
            foreach ($mField as $field) {
                //echo "<br/>\n --- $field";
                $fieldEx = '_'.$field;

                dump(" FIELD Ex = $fieldEx");

                //Chắc chắn là nếu method có tồn tại ở meta
                //Thì objData sẽ có dữ liệu có tên tương ứng
                if (method_exists($meta, $fieldEx)) {
                    //                    dump($one[$fieldEx]);
                    echo "<br/>\n Method Found: $fieldEx()";
                    self::assertTrue(array_key_exists($fieldEx, $one));
                }
            }


            //            dump($one);
        }

    }

    /**
     * kiểm tra API có trả lại các trường mở rộng với field bắt đầu bằng _
     * Xem get one api trả lại các trường extra đúng như được khai báo, cấp quyền không:
     */
    public function testGetOneItemApiDemo($gid = 1)
    {

        $demo = DemoTbl::latest()->first();
        $url = "/api/demo/get/$demo->id";

        if ($gid == 1) {
            $user = User::getUserByEmail('admin@abc.com');
        }
        if ($gid == 3) {
            $user = User::getUserByEmail('member@abc.com');
        }

        $tk = $user->getUserToken();

        $user->addAllowPermissionRouteNameOnUser('api.demo.get');
        $response = $this->withToken("$tk")->getJson($url);
        $this->assertEquals(200, $response->getStatusCode());
        $user->removePermissionRouteNameOnUser('api.demo.get');

        $ret = $response->decodeResponseJson();

        //        dump($ret);

        self::assertTrue($ret['payload'] != null);

        //        dump($ret['payload']);

        $one = $ret['payload'];

        $meta = DemoTbl::getMetaObj();
        $mField = $meta->getShowGetOneAllowFieldList($gid);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r(array_values($mField));
        //        echo "</pre>";

        dump($one);
        //Một item sẽ có các trường như Meta
        //item sẽ có các trường tương ứng các hàm data extra của Meta
        foreach ($mField as $field) {
            //echo "<br/>\n --- $field";
            $fieldEx = '_'.$field;

            dump(" FIELD Ex = $fieldEx");

            //Chắc chắn là nếu method có tồn tại ở meta
            //Thì objData sẽ có dữ liệu có tên tương ứng
            if (method_exists($meta, $fieldEx)) {
                //                    dump($one[$fieldEx]);

                echo "<br/>\n Method Found: $fieldEx()";

                self::assertTrue(array_key_exists($fieldEx, $one));
            }
        }
        //            dump($one);

    }

    public function testGetIndexApiDemoMember()
    {
        $this->testGetIndexApiDemo(3);
    }

    public function testGetOneItemApiDemoMember()
    {
        $this->testGetIndexApiDemo(3);
    }
}
