<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Testing\TestResponse;

class LoginTest extends TestCase1
{
    /**
     * @var TestResponse
     */
    public function getAfterLoginResponse()
    {
        return self::$respondAfterLogin;
    }

    public function testLoginWrongAccount()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $url = request()->fullUrl();
        //        echo "<br/>\n BaseURl2 : $url";

        Session::start();

        $response = $this->followingRedirects()->call('POST', '/post-login', [
            'email' => 'admin@abc.com',
            'password' => time(),
            '_token' => csrf_token(),
        ]);

        //        dump($response->content());

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($response->content());
        //        echo "</pre>";

        //Nếu sai, lại về /login
        $this->assertEquals(200, $response->getStatusCode());

        dump($response->original->name());

        // $this->assertEquals('login.login', $response->original->name());

    }

    public function testAccessAdminDbPermission()
    {

        //Khi đã login true acc, thì truy cập vào admin được không:
//        $this->testLoginTrueAccount();

        $this->actingAs(User::where('email', 'member@abc.com')->first());
        //Truy cập admin
        $response = $this->get('/admin/db-permission');
        $response->assertStatus(403);

        $this->actingAs(User::where('email', 'admin@abc.com')->first());
        $response = $this->get('/admin/db-permission');
        $response->assertStatus(200);

        //        $response->assertSee("Chọn bảng");
        //
        //        $response = $this->get("/admin/demo-api");
        //        $response->assertSee("Đà nẵng");
        //        $response->assertSee("abc.com");

        //        $this->assertEquals('true', $browser->attribute('#fieldID', 'readonly'));

    }

    public function testAccessDemoTable()
    {
        //        $this->testLoginTrueAccount();
        //        $response = $this->get("/admin/demo-api");
        //        dump("RET = ". $response->status());
        //        $response->assertStatus(200);
//        $this->assertTrue(true);
    }
}
