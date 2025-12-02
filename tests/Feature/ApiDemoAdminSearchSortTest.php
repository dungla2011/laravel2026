<?php

namespace Tests\Feature;

use App\Http\ControllerApi\DemoControllerApi;
use App\Models\DemoTbl;
use App\Models\User;
use Mockery\MockInterface;
use Tests\TestCase;

class ApiDemoAdminSearchSortTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // set your headers here
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
     * A basic test example.
     *
     * @return void
     */
    public function testGetIndex()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $url = route('api.demo.list');
        $response = $this->getJson($url);
        //        $this->assertEquals(403, $response->getStatusCode());
        //
        //        //$response = $this->withToken("123456")->getJson(route("api.demo.list"));
        //        $response = $this->withHeader('Authorization','Bearer 123456')->getJson(route("api.demo.list"));
        $this->assertEquals(200, $response->getStatusCode());

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($response->content());
        //        echo "</pre>";
        //
        $ret = $response->decodeResponseJson();
        $dataAll = $ret['payload'];

        $this->assertDataApi($ret);

        dump('Total = '.$dataAll['total']);

        $data = $dataAll['data'];

        $mmDb = DemoTbl::all()->toArray();

        $this->assertTrue(($dataAll['total']) == count($mmDb));

        $foundInDb = 0;
        foreach ($data as $mItem) {
            foreach ($mmDb as $dbItem) {
                if ($mItem['id'] == $dbItem['id']) {
                    dump('FOUND DB: '.$dbItem['id']);
                    $foundInDb++;
                    break;
                }
            }
        }
        $this->assertTrue($foundInDb == count($data));
    }

    public function testSortIndex($fieldSort = 'created_at', $type = 'desc')
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField($fieldSort);

        $url = route('api.demo.list');

        $url .= '?'.DEF_PREFIX_SORTBY_URL_PARAM_GLX.$sname."=$type";

        dump("URL: $url");

        $response = $this->getJson($url);
        //        $this->assertEquals(403, $response->getStatusCode());
        //
        //        //$response = $this->withToken("123456")->getJson(route("api.demo.list"));
        //        $response = $this->withHeader('Authorization','Bearer 123456')->getJson(route("api.demo.list"));
        $this->assertEquals(200, $response->getStatusCode());

        $mmDb = DemoTbl::orderBy($fieldSort, $type)->get()->toArray();

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($response->content());
        //        echo "</pre>";
        //
        $ret = $response->decodeResponseJson();

        $this->assertDataApi($ret);

        $dataAll = $ret['payload'];
        dump('Total = '.$dataAll['total']);

        $data = $dataAll['data'];

        //Khi sort, thì chắc chắn là DB và API có TOP ID bằng nhau
        dump('ID TOP = '.$data[0]['id']);
        $this->assertTrue($data[0]['id'] == $mmDb[0]['id']);

    }

    public function testSortIndex1()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $this->testSortIndex('created_at', 'asc');
    }

    public function testSearchIndex($field = 'created_at')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //$mm = DemoTbl::all();

        $mm = DemoTbl::orderBy('id', 'desc')->limit(10)->get();

        $m1 = $mm[0];
        $timeUpdate = nowyh();
        $oldTime = $m1->$field;
        $m1->$field = $timeUpdate;
        $m1->update();

        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField($field);
        $url = route('api.demo.list');
        $url .= '?'.DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."=$timeUpdate";

        dump("URL: $url");

        $response = $this->getJson($url);
        $ret = $response->decodeResponseJson();
        $this->assertDataApi($ret);
        $dataAll = $ret['payload'];

        dump('Total = '.$dataAll['total']);

        $data = $dataAll['data'];

        $this->assertEquals(200, $response->getStatusCode());

        $one = DemoTbl::where($field, '=', $timeUpdate)->first();

        $m1->$field = $oldTime;
        $m1->update();

        dump('ID Found: '.$one['id'].'/'.$data[0]['id']);
        $this->assertTrue($one['id'] == $data[0]['id']);
    }

    public function testSearchGtIndex($field = 'created_at')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //        $mm = DemoTbl::all();
        $mm = DemoTbl::orderBy('id', 'desc')->limit(10)->get();

        $m1 = $mm[0];
        $timeUpdate = nowyh();

        $timeSmaller = nowyh(time() - 1);

        $oldTime = $m1->$field;
        $m1->$field = $timeUpdate;
        $m1->update();

        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField($field);
        $url = route('api.demo.list');
        $url .= '?'.DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."=$timeSmaller&".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname.'=gt';

        dump("URL: $url");

        $response = $this->getJson($url);
        $this->assertEquals(200, $response->getStatusCode());

        $ret = $response->decodeResponseJson();
        $this->assertDataApi($ret);
        $dataAll = $ret['payload'];
        dump('Total = '.$dataAll['total']);
        $data = $dataAll['data'];

        $one = DemoTbl::where($field, '>', $timeSmaller)->first();

        $m1->$field = $oldTime;
        $m1->update();
        dump('ID Found: '.$one['id']);
        $this->assertTrue($one['id'] == $data[0]['id']);
    }

    public function testSearchLikeIndex($field = 'created_at')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //        $mm = DemoTbl::all();
        $mm = DemoTbl::orderBy('id', 'desc')->limit(10)->get();

        $m1 = $mm[0];
        $timeUpdate = nowyh();
        $timeSearch = substr($timeUpdate, 1, -1);

        $oldTime = $m1->$field;
        $m1->$field = $timeUpdate;
        $m1->update();

        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField($field);
        $url = route('api.demo.list');
        $url .= '?'.DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname."=$timeSearch&".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname.'=C';

        dump("URL: $url");

        $response = $this->getJson($url);

        dump('next ');

        if ($response->getStatusCode() != 200) {
            dump('Error from server: '.$response->getContent());
        }

        $this->assertEquals(200, $response->getStatusCode());

        $ret = $response->decodeResponseJson();

        $this->assertDataApi($ret);

        $dataAll = $ret['payload'];
        dump('Total = '.$dataAll['total']);
        $data = $dataAll['data'];

        $one = DemoTbl::where($field, 'LIKE', "%$timeSearch%")->first();

        $m1->$field = $oldTime;
        $m1->update();
        dump('ID Found: '.$one['id']);
        $this->assertTrue($one['id'] == $data[0]['id']);
    }

    public function testSearchLikeWrongOperator($field = 'created_at')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField($field);
        $url = route('api.demo.list');
        $url .= '?'.DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname.'=123&'.DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname.'=C123';
        dump("URL: $url");
        $response = $this->getJson($url);
        dump('next ');
        $this->assertTrue($response->getStatusCode() == 400);
        $this->assertTrue(strstr($response->getContent(), 'Not valid Search Operator') !== false);
    }

    public function testMock1()
    {

        //        $this->mock(DemoTbl::class, \Mockery::class);

        $mock = $this->mock(DemoTbl::class, function (MockInterface $mock) {
            $mock->shouldReceive('test123')->once()->andReturn('ABC123');
        });

        dump($mock->test123());
        //
        //        dump($mock);
        //
        //        $this->app->bind('model_with_controller_'.DemoControllerApi::class, $mock);

        //        $this->mock(DemoTbl::class, function ($mock) {
        //            $mock
        //                // Assert that GetExchangeRate's `execute(..)` method is called
        //                ->shouldReceive('execute')
        //                // with argument 'USD'
        //                //->with('USD')
        //                // And return this (instead of actally executing it)
        //                ->andReturn([
        //                    111=>222,
        //                ]);
        //        });

    }

    /**
     * Kiểm tra API search multi
     * Tìm uid > 1 + status > 1
     */
    public function testSearchMulti()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        //Để test multi, Chắc chắn db có ít nhất 1 bản ghi user_id > 1; status = 1;
        //có thêm created_at vì API mặc định vậy
        if (! $qr = DemoTbl::where('status', 1)->where('user_id', '>', 1)->orderBy('created_at', 'desc')->first()) {
            $obj = DemoTbl::first();
            $obj->user_id = 2;
            $obj->status = 1;
            $obj->update();
        }
        $qr = DemoTbl::where('status', 1)->where('user_id', '>', 1)->orderBy('created_at', 'desc')->first();

        $objDb = $qr->toArray();

        $this->assertTrue($qr != null, ' Phải có một đối tượng này để test: status = 1, userid > 1');

        $objMeta = DemoTbl::getMetaObj();
        $sname = $objMeta->getShortNameFromField('user_id');
        $snameStatus = $objMeta->getShortNameFromField('status');
        $url = route('api.demo.list');
        //API tìm userid > 1 và status = 1
        $url .= '?'.DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname.'=1&'.DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname.'=gt';
        $url .= '&'.DEF_PREFIX_SEARCH_URL_PARAM_GLX.$snameStatus.'=1';
        dump($url);
        $res = $this->getJson($url);
        $objResponse = json_decode($res->getContent());

        //        dump($objResponse);
        $objDemo = $objResponse->payload->data[0];

        dump(' DEMO OBJ = ');
        dump($objDemo);

        //vì là join joinuseremailuserid, nên trả lại giá trị uid=>email
        //12.11.22: đổi lại, join vẫn phải giữ nguyên giá trị , ko thay đổi giá trị
        //todo: cần sửa lại ở đây

        dump(array_keys(get_object_vars($objDemo->_user_id))[0]);

        $this->assertTrue(array_keys(get_object_vars($objDemo->_user_id))[0] > 1);
        $this->assertTrue($objDemo->status == 1);
        $this->assertTrue($objDemo->id == $objDb['id']);
        //        $this->assertTrue($objDemo->user_id ==$objDb['user_id']);
        $this->assertTrue(array_keys(get_object_vars($objDemo->_user_id))[0] == $objDb['user_id']);
        $this->assertTrue($objDemo->status == $objDb['status']);

    }

    public function testSearchEmpty()
    {

        //Todo:
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $field1 = 'created_at';
        $field2 = 'number2';

        $this->assertTrue(true);
    }

    public function testSearchNull()
    {
        //Todo:
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $field1 = 'created_at';
        $field2 = 'number2';

        $this->assertTrue(true);
    }
}
