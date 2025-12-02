<?php

namespace Tests\Feature;

use App\Models\FileUpload;
use App\Models\News;
use App\Models\User;
use App\Support\HTMLPurifierSupport;
use Tests\TestCase;

class ApiBaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // set your headers here
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        //Tao userid 1 neu chua co:

        User::createUserAdminDefault();
        User::createUserMemberForTest();

        $tk = User::where("email", 'admin@abc.com')->first()->getUserToken();
        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        $this->withHeader('Authorization', 'Bearer '.$tk);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDemoIndex()
    {

        $this->assertTrue(true);
        //
        //        dump("=== Check " . get_called_class() ."/" . __FUNCTION__);
        //        $url = route("api.demo.list");
        //
        //        dump("URL = $url")        ;
        //
        //        $response = $this->getJson($url);
        //
        ////        $response->dumpHeaders();
        //
        ////        $this->assertEquals(403, $response->getStatusCode());
        ////
        ////        $response = $this->withToken("123456")->getJson(route("api.demo.list"));
        ////        $response = $this->withHeader('Authorization','Bearer 123456')->getJson(route("api.demo.list"));
        //        $this->assertEquals(200, $response->getStatusCode());
        //
        ////        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        ////        print_r($response->content());
        ////        echo "</pre>";
        ////
        //        $ret = $response->decodeResponseJson();

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($ret);
        //        echo "</pre>";

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($js);
        //        echo "</pre>";

        //$this->assertTrue(true);
    }


    public function testUpdateArrayBillAndFillUsedNumber()
    {

        $mm = [
            ['id' => 1, 'allow' => 10, 'quantity' => 1, 'used' => 0],

        ];

        $ret = updateArrayBillAndFillUsedNumber(10, $mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['free'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(11, $mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['free'] == 1);


        $mm = [
 ['id' => 1, 'allow' => 10, 'quantity' => 1, 'used' => 0],
 ['id' => 2, 'allow' => 50, 'quantity' => 1, 'used' => 0],
 ['id' => 3, 'allow' => 10, 'quantity' => 2, 'used' => 0],
 ['id' => 4, 'allow' => 10, 'quantity' => 1, 'used' => 0],
 ];
        $ret = updateArrayBillAndFillUsedNumber(0, $mm);
        self::assertTrue($ret[0]['used'] == 0);
        self::assertTrue($ret[1]['used'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(5,$mm);
        self::assertTrue($ret[0]['used'] == 5);
        self::assertTrue($ret[1]['used'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(10,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(11,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 1);

        $ret = updateArrayBillAndFillUsedNumber(20,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 10);

        $ret = updateArrayBillAndFillUsedNumber(50,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 40);

        $ret = updateArrayBillAndFillUsedNumber(60,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(61,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 1);

        $ret = updateArrayBillAndFillUsedNumber(70,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 10);
        self::assertTrue($ret[3]['used'] == 0);


        $ret = updateArrayBillAndFillUsedNumber(80,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 20);
        self::assertTrue($ret[3]['used'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(81,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 20);
        self::assertTrue($ret[3]['used'] == 1);

        $ret = updateArrayBillAndFillUsedNumber(90,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 20);
        self::assertTrue($ret[3]['used'] == 10);
        self::assertTrue($ret[4]['free'] == 0);

        $ret = updateArrayBillAndFillUsedNumber(91,$mm);
        self::assertTrue($ret[0]['used'] == 10);
        self::assertTrue($ret[1]['used'] == 50);
        self::assertTrue($ret[2]['used'] == 20);
        self::assertTrue($ret[3]['used'] == 10);
        self::assertTrue($ret[4]['free'] == 1);

    }

    public function testHtmlPurifier()
    {

        //Nếu enable mặc định thì HTML_PURIFIER sẽ strip hết các cái ko hợp lệ
        $GLOBALS['DEF_DISABLE_HTML_PURIFIER'] = 0;

        $url = '/api/news/add';
        $date = '1970-01-01 11:11:11';
        $name1 = '<script></script>ABC123';
        $content1 = '<script></script>ABC12345 <div>abc</div> <span onclick="alert(123)"></span>';
        $ctClean = HTMLPurifierSupport::clean($content1);

        //Chac chan ko tim thay cac chuoi js:
        self::assertTrue(strstr($ctClean, 'onclick') === false);
        self::assertTrue(strstr($ctClean, '<script>') === false);

        $res = $this->post($url,
            [
                'name' => $name1,
                'created_at' => $date,
                'content' => $content1,
            ]);
        $js = json_decode($res->content());
        dump($js);
        if ($js->payload) {
            if ($obj = News::find($js->payload)) {
                if ($obj->created_at == $date) {
                    self::assertTrue($obj->name == strip_tags($name1), ' Name in db strip not ok?');
                    self::assertTrue($obj->content == $ctClean, ' Content in db strip not ok?');
                    $obj->forceDelete();
                }
            }
        }

        //Nếu không enable HTML_PURIFIER, thì mọi giá trị được giữ nguyên si
        $GLOBALS['DEF_DISABLE_HTML_PURIFIER'] = 1;
        $name1 = 'ABC123';
        $res = $this->post($url,
            [
                'name' => $name1,
                'created_at' => $date,
                'content' => $content1,
            ]);
        $js = json_decode($res->content());
        dump($js);
        if ($js->payload) {
            if ($obj = News::find($js->payload)) {
                if ($obj->created_at == $date) {

                    dump("Check content2: $content1");
                    self::assertTrue($obj->content == $content1, ' Content2 in db not ok?');
                    $obj->forceDelete();
                }
            }
        }

        self::assertTrue($res->status() == 200, ' status = '.$res->status());

    }

    /**
     * Chắc chắn User member có 1 số quyền cơ bản ở role, và index show
     */
    public function testMemberHasSomeRole()
    {
        return;

        $obj = User::findUserWithEmail('member@abc.com');

        self::assertTrue($obj != null);

        $mRoute = ['api.member-file.upload',
            'member.file.edit',
            'api.member-file.get',
        ];

        $mm = $obj->getAllRouteNameAllowThisUser();
                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($mm);
            echo "</pre>";

        foreach ($mRoute as $route) {
            //            $route = "api.member-file.upload";
//            if(!$obj->addAllowPermissionRouteNameOnUser($route))
//            {
//                die("Can not add roles?");
//            }




            self::assertTrue(in_array($route, $mm));
        }

        //Member cần có quyền show Index file, name

        $objMeta = FileUpload::getMetaObj();
        //        $objMeta->setAllowGidOnIndexField(['id', 'name'], 3, 1);


        self::assertTrue($objMeta->isShowIndexField('id', 3) != '');
        self::assertTrue($objMeta->isShowIndexField('name', 3) != '');

    }

    public function testPasswordFieldMustEmptyApi()
    {
        //Todo...

        $this->assertTrue(true);
    }
}
