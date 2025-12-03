<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use LadLib\Laravel\HelperLaravel2;
use Tests\TestCase;


function routeHasCanMiddleware(string $routeName): bool
{
    $route = Route::getRoutes()->getByName($routeName);

    if (!$route) {
        throw new InvalidArgumentException("Route {$routeName} not found.");
    }

    $middlewares = $route->gatherMiddleware();

    foreach ($middlewares as $middleware) {
        if (str_starts_with($middleware, 'can:')) {
            return true;
        }
    }

    return false;
}

class RoleTest extends TestCase
{
    /**
     * Kiểm tra member role có thể vào, guest ko thể vào
     *
     * @return void
     */
    public function testMemberRole1()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        $user = User::findUserWithEmail('member@abc.com');
        self::assertTrue($user != null);
        $tk = $user->getUserToken();
        dump("Token : $tk");
        //$this->withToken("123456")->getJson(route("api.demo.list"));
        //$this->withHeader('Authorization' ,'Bearer ' . $tk);

        $guest = new User();

        //dump($role->permissions()->get()->toArray());
        $allPer = $user->getAllRouteNameAllowThisUserAndUrl();

        foreach ($allPer as $url => $route_name_code) {

            dump("route_name_code = $url/ ".$route_name_code);

            if (! Route::has($route_name_code)) {
                continue;
            }

            if (strstr($url, '{')) {
                continue;
            }

            $url = route($route_name_code);

            dump('route_name_code = '.$route_name_code." /url = $url");
            //Bỏ qua demo gate
            if (str_starts_with($route_name_code, 'admin.demogate')) {
                continue;
            }

            $user->addAllowPermissionRouteNameOnUser($route_name_code);

            $methods = HelperLaravel2::getRouteMethodsFromRouteName($route_name_code);
            //                dump($methods);

            //$method = strtolower($method);

            if (str_starts_with($route_name_code, 'api.')) {
                if (in_array('GET', $methods) !== false) {
//                    $response = $this->withToken($tk)->get($url);
                    $response = testFileGetContent($url, $user);
                    dump(substr($response->getContent(), 0, 500));
                    $this->assertTrue($response->getStatusCode() == 200 || $response->getStatusCode() == 400);
                    //Guest sẽ ko vào được
                    $this->session(['laravel_session' => null]);
                    $response = $this->withToken('.')->get($url);
                    $this->assertEquals(403, $response->getStatusCode());
                }

                if(0)
                if (in_array('POST', $methods) !== false) {

                    $response = $this->withToken($tk)->post($url);

                    dump(substr($response->getContent(), 0, 500));

                    $this->assertTrue($response->getStatusCode() == 200 || $response->getStatusCode() == 400);

                    //Guest sẽ ko vào được
                    $this->session(['laravel_session' => null]);
                    $response = $this->withToken('.')->post($url);
                    $this->assertEquals(403, $response->getStatusCode());
                }
            } else {
                if (in_array('GET', $methods) !== false) {

//                    $response = $this->actingAs($user)->get($url);
                    $response = testFileGetContent($url, $user);
                    dump(substr($response->getContent(), 0, 500));
                    $this->assertTrue(
                        $response->getStatusCode() == 200
                        || $response->getStatusCode() == 400
                        || $response->getStatusCode() == 302
                    );

                    $this->session(['laravel_session' => null]);
                    //Guest sẽ ko vào được
                    $this->actingAs($guest);
                    $response = $this->get($url);
                    $this->assertEquals(403, $response->getStatusCode());
                }

                if(0)
                if (in_array('POST', $methods) !== false) {
                    $response = $this->actingAs($user)->post($url);
                    dump(substr($response->getContent(), 0, 500));

                    dump('STATUS = '.$response->getStatusCode());

                    $this->assertTrue($response->getStatusCode() == 200 || $response->getStatusCode() == 400
                        || $response->getStatusCode() == 302
                    );

                    $this->session(['laravel_session' => null]);
                    $this->actingAs($guest);
                    //Guest sẽ ko vào được
                    $response = $this->post($url);
                    $this->assertEquals(403, $response->getStatusCode());
                }
            }
        }
    }

    public function testGuestCanAccessSomeThing()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        $routeCollection = Route::getRoutes();

        $cc = $cc1 = $cc2 = $cc3 = 0;
        $tt = count($routeCollection);
        foreach ($routeCollection as $route) {
            if ($route instanceof \Illuminate\Routing\Route);
            $rName = $route->getName();

            if (! $rName) {
                continue;
            }

            if (str_starts_with($rName, 'admin.demogate')) {
                continue;
            }

            if ($rName == 'admin.index') {
                $response = $this->withToken('.')->get(\route($rName));
                $this->assertEquals(403, $response->getStatusCode());

                //                dump("CURL = " . url()->current());
                //                $this->followRedirects($response);
                //                $this->assertStringContainsString('/login', url()->current());
                continue;
            }


            if (str_starts_with($rName, 'api') ||
                str_starts_with($rName, 'admin') ||
                str_starts_with($rName, 'member')
            ) {


                if($cc1 > 30 && $cc2 > 30 && $cc3 > 30){
                    break;
                }

                //Mỗi cái test 30 thoi
                if(str_starts_with($rName, 'api')){
                    $cc1++;
                    if($cc1 > 30){
                        continue;
                    }
                }
                if(str_starts_with($rName, 'admin')){
                    $cc2++;
                    if($cc2 > 30){
                        continue;
                    }
                }
                if(str_starts_with($rName, 'member')){
                    $cc3++;
                    if($cc3 > 30){
                        continue;
                    }
                }

                $cc++;
                dump($rName);
                $url = $route->uri();
                dump("$cc/$tt. URL1x = ".$url);

                //nếu ko có CAN thì bỏ qua
                if(!routeHasCanMiddleware($rName)){
                    dump("Ignnore this route $rName");
                    continue;
                }
                else{
                    dump("Can: ok");
                }

                $methods = HelperLaravel2::getRouteMethodsFromRouteName($rName);
                if (in_array('GET', $methods) !== false) {
                    dump("GET...");
                    //Guest sẽ ko vào được
                    $response = $this->withToken('.')->get($url);
                    if (! User::checkGuestPermissionRoute($rName)) {
                        $this->assertTrue($response->getStatusCode() != 200);
                    }
                    //                    $this->assertEquals(403, $response->getStatusCode());
                }
                if (in_array('POST', $methods) !== false) {
                    dump("POST...");
                    //Guest sẽ ko vào được
                    $response = $this->withToken('.')->post($url);

                    $this->assertTrue($response->getStatusCode() != 200);
                }

            }

        }

    }

    /**
     * Check enable roles với Member, sau đó disable, để chắc chắn On/Off quyền với user đó
     * Check admin có thể vào mọi quyền, sau đó Toogle quyền với Member... và kiểm tra 200/403
     */
    public function testMemberRoleEnableDisable()
    {
        $rName = 'api.admin-folder-file.tree-index';
        $member = User::where('email', 'member@abc.com')->first();
        $member->addAllowPermissionRouteNameOnUser($rName);
        $url = route($rName);




        $resp = testFileGetContent($url, $member);

        dump("\n TK: " . $member->getUserToken());
//        dump(substr($resp->getContent(), 0, 500));
//
//        die();

//        return;



        //Tìm 1 role bất kỳ admin có thể truy cập được, guest ko thể truy cập
        //Xem nó có access bởi Member hay không

        $member = User::where('email', 'member@abc.com')->first();
        $admin = User::where('email', 'admin@abc.com')->first();

        $this->assertTrue($member != null);

        //Chắc chắn member ko phải isadmin
        $this->assertTrue(isset($member->is_admin) && ! $member->is_admin);

        $rs = Route::getRoutes();

        $cc = 0;
        $limit = 100; //Chi check 100 route thoi
        foreach ($rs as $route) {
            if ($route instanceof \Illuminate\Routing\Route);
            $rName = $route->getName();
            if (! $rName) {
                continue;
            }
            if (! str_starts_with($rName, 'api') &&
                ! str_starts_with($rName, 'admin') &&
                ! str_starts_with($rName, 'member')
            ) {
                continue;
            }
            if (str_starts_with($rName, 'admin.demogate')) {
                continue;
            }

            if (
                str_contains($rName, 'tree'))
                continue;

            if (
                str_contains($rName, '/user/')
                ||
                str_contains($rName, 'file/')
                ||
                str_contains($rName, 'news')
            )
            {

            }
            else
                continue;

            //Bỏ qua route có param
            if (strstr($route->uri(), '{')) {
                continue;
            }

            if (! Route::has($rName)) {
                continue;
            }

            $methods = HelperLaravel2::getRouteMethodsFromRouteName($rName);
            //Bỏ qua Route ko phải GET
            if (in_array('GET', $methods) === false) {
                continue;
            }

            if (in_array('POST', $methods)) {
                continue;
            }

            //            dump($rName);

            //Kiểm tra mideware không có check quyền thì bỏ qua:
            if (! strstr(serialize($route->middleware()), 'can:')) {
                continue;
            }

            $url = route($rName);

            if(str_contains($url, '/create'))
                continue;
            if(str_contains($url, '/rename'))
                continue;
            if(str_contains($url, 'delete'))
                continue;

            $cc++;
            if ($cc > $limit) {
                break;
            }

//            if(!str_contains($url, 'api/member-file-refer/list'))
//                continue;

//            $resp = $this->actingAs($admin)->first()->get($url);
            $resp = testFileGetContent($url, $admin);

            dump("URLx123: $url");

//            dump($methods);

//            dump(substr($resp->getContent(), 0, 100));


            if($resp->getStatusCode() != 200){
                die("\n\n URL= $url GLX: ... " . $resp->getStatusCode());
            }

            //assert Admin truy cap moi route
            $this->assertEquals(200, $resp->getStatusCode());



            //            dump(" - Admin Status: " . $resp->getStatusCode());

            dump("Rname/URL :$rName / $url");

            if ($resp->getStatusCode() == 200) {

//                $resp = $this->actingAs($member)->get($url);
                $resp = testFileGetContent($url, $member);

                dump(' Member Status: '.$resp->getStatusCode());

                if ($resp->getStatusCode() == 200) {
                    //Remove quyền trên route này, và check lại 403
                    $member->removePermissionRouteNameOnUser($rName);

                    //                    sss(20);
                    //Reload lại member
//                    $member = User::where('email', 'member@abc.com')->first();

                    dump('status1: '.$resp->getStatusCode());
//                    $resp = $this->actingAs($member)->get($url);
                    $resp = testFileGetContent($url, $member);

                    dump('status2: '.$resp->getStatusCode());
                    if (! User::checkGuestPermissionRoute($rName)) {
                        $this->assertEquals(403, $resp->getStatusCode());
                    }

                    //                    sss(20);
                    //Add trả lại nguyên bản quyền
                    $member->addAllowPermissionRouteNameOnUser($rName);

                } else {

//                    sss(30);
                    dump(' NOT 200, cont ...');
                    //Add quyền
                    if (! $member->addAllowPermissionRouteNameOnUser($rName)) {
                        exit('**** can not add role???');
                    }
                    dump(' 1 ...');

                    //                    if(strstr($url, 'min/demo-and-tag'))
                    //                        sss(1000);

                    //                    $this->refreshApplication();

                    //Reload lại member
//                    $member = User::where('email', 'member@abc.com')->first();
//
//                    if (! $member) {
//                        exit('Not found member: fortest@abc...');
//                    }

//                    sss(1);
                    //                    usss(100000);
                    //                    dump(" 2 ... $url");
//                    $resp = $this->actingAs($member)->get($url);
                    $resp = testFileGetContent($url, $member);
                    //                    dump(" 3 ...");
                    //                    sss(5);
                    //                    dump("STATUS: " . $resp->getStatusCode());
                    //Remove quyền trả lại gốc


//                    usleep(100000);
                    //                    dump(" 4 ...");
                    //                    $resp->dump();
                    dump(substr($resp->getContent(), 0, 500));
                    dump(" 21... $url");
                    echo "\n 211...$rName /  $url";
                    echo "\n ".substr($resp->getContent(), 0, 500);
                    $this->assertEquals(200, $resp->getStatusCode());

                    $member->removePermissionRouteNameOnUser($rName);
//                    sss(30);
                }
            }
        }
    }

    /**
     * Kiểm tra quyền (Enable/Disable) của user trên Tất cả các Route (url, post hoặc get)
     * Nếu query url, có status=200, là có quyền, thì khi disable quyền đó, thì query phải trả về 403 (không có quyền)
     * Nếu query url, có status<>200, thì khi enable quyền, phải trả về code khác 403 (nghĩa là có quyền truy cập được, nếu có lỗi thì phải  <> 403)
     */
    public function test2MemberRoleEnableDisable()
    {

        $member = User::where('email', 'member@abc.com')->first();
        $this->assertTrue($member != null);

        //$url = ....
        //$res = $this->actingAs($member)->get($url);

        //Chắc chắn member ko phải isadmin
        $this->assertTrue(isset($member->is_admin) && ! $member->is_admin);

        $rs = Route::getRoutes();

        $cc = 0;
        $limit = 100; //Chi check 100 route thoi
        foreach ($rs as $route) {
            if ($route instanceof \Illuminate\Routing\Route);
            $rName = $route->getName();
            if (! $rName) {
                continue;
            }
            if (! str_starts_with($rName, 'api') &&
                ! str_starts_with($rName, 'admin') &&
                ! str_starts_with($rName, 'member')
            ) {
                continue;
            }
            if (str_starts_with($rName, 'admin.demogate')) {
                continue;
            }

            //Bỏ qua route có param
            if (strstr($route->uri(), '{')) {
                continue;
            }

            $methods = HelperLaravel2::getRouteMethodsFromRouteName($rName);

            //Chỉ kiểm tra Post hoặc Get
            if (in_array('GET', $methods) !== false
                ||
                in_array('POST', $methods) !== false
            ) {

            } else {
                continue;
            }

            //            dump($rName);

            //Kiểm tra mideware không có check quyền thì bỏ qua:
            if (! strstr(serialize($route->middleware()), 'can:')) {
                continue;
            }

            $cc++;
            if ($cc > $limit) {
                break;
            }
            $url = route($rName);

            dump("\n Rname/URL :$rName / $url");

            //Todo: xxxxxx ko hieu sao admin block ui lại lấy keyname ở đâu
            if ($rName !== 'admin.block-ui.index') {
                continue;
            }

            if (in_array('GET', $methods) !== false) {
                $resp = $this->actingAs($member)->get($url);
            } else {
                $resp = $this->actingAs($member)->post($url);
            }

            dump("\n  Member Status: ".$resp->getStatusCode());

            if ($resp->getStatusCode() == 200) {

                //Remove quyền trên route này, và check lại 403
                $member->removePermissionRouteNameOnUser($rName);

                //                    sss(20);
                //Reload lại member
                $member = User::where('email', 'member@abc.com')->first();

                dump("\n status1: ".$resp->getStatusCode());

                if (in_array('GET', $methods) !== false) {
                    $resp = $this->actingAs($member)->get($url);
                } else {
                    $resp = $this->actingAs($member)->post($url);
                }

                dump("\n status2: ".$resp->getStatusCode());

                //Nếu guest được quyền thì luôn là 200
                if (User::checkGuestPermissionRoute($rName)) {
                    $this->assertEquals(200, $resp->getStatusCode());
                } else {
                    $this->assertEquals(403, $resp->getStatusCode());
                }

                //                    sss(20);
                //Add trả lại nguyên bản quyền
                $member->addAllowPermissionRouteNameOnUser($rName);

            } else {

                dump(" $rName NOT 200, cont ...");
                //Add quyền
                if (! $member->addAllowPermissionRouteNameOnUser($rName)) {
                    exit('**** can not add role???');
                }
                dump(" $rName 1 ...");

                //                    if(strstr($url, 'min/demo-and-tag'))
                //                        sss(1000);

                //                    sss(5);
                //Reload lại member
                $member = User::where('email', 'member@abc.com')->first();
                //                    usss(100000);
                dump("\n 2 ... $url");

                $this->refreshApplication();

                $this->withCookie('_tglx863516839', $member->getJWTUserToken());

                if (in_array('GET', $methods) !== false) {
                    $resp = $this->actingAs($member)->get($url);
                } else {
                    $resp = $this->actingAs($member)->post($url);
                }

                echo "\n 3 ...$url / ".__LINE__;

                print_r($methods);

                //                    sss(5);

                //Remove quyền trả lại gốc

                if (! in_array($resp->getStatusCode(), [200, 400, 419, 302])) {
                    echo "\n ---- ".(substr($resp->getContent(), 0, 1000));
                }

                //$this->assertTrue($resp->getStatusCode() != 403);

                echo  "\n\n $rName RET = ".$resp->getStatusCode();

                //Chỉ cho phép các code:
                // 200 là không lỗi,
                // 400 là lỗi UserInput trả lại bởi code trong API,
                // 419 là lỗi CFRS...
                if (in_array('POST', $methods) !== false) {

                    if (! in_array($resp->getStatusCode(), [200, 400, 419, 302])) {
                        //                        sss(1111);
                    }

                    $this->assertTrue(in_array($resp->getStatusCode(), [200, 400, 419, 302]), ' RET = '.$resp->getStatusCode());
                } else {
                    echo "\n 41 ...".substr($resp->getContent(), 0, 1000);
                    dump("\n 42 ...".substr($resp->getContent(), 0, 1000));
                    $this->assertTrue(in_array($resp->getStatusCode(), [200, 400, 302]));
                }

                $member->removePermissionRouteNameOnUser($rName);
                //Không có báo lỗi Internal server error, là lỗi của CODE
                $this->assertTrue($resp->getStatusCode() != 500);
            }
        }

        //Xóa hết quyền của UserTest
        $member->removeAllPermissionOnUser();

    }

    /**
     * Test guest can access/unaccess some role
     */
    public function tGuestAccessSomeRole()
    {
        // admin.demo-api.index	url: admin/demo-api
        //http://localhost:9081/admin/demo-api
        //admin.demo-folder.index	url: admin/demo-folder

        $guest = User::createGuestForTest();
        $mm = ['api.demo.list', 'admin.demo-api.index'];
        foreach ($mm as $rn) {
            $url = HelperLaravel2::getUrlFromRouteName($rn);

            if ($guest::checkGuestPermissionRoute($rn)) {

                //Có thể truy cập:
                $resp = $this->get($url);

                echo "\n\nROLE = $rn / $url / " . $resp->getStatusCode();

                $this->assertTrue($resp->getStatusCode() == 200);

                $guest->removePermissionRouteNameOnUser($rn);
                //Không thể truy cập:
                $resp = $this->get($url);
                $this->assertTrue($resp->getStatusCode() == 403);

                //Trả lại
                $guest->addAllowPermissionRouteNameOnUser($rn);
            } else {
                //Không thể truy cập:
                $resp = $this->get($url);
                $this->assertTrue($resp->getStatusCode() == 403);
                $guest->addAllowPermissionRouteNameOnUser($rn);
                //Có thể truy cập:
                $resp = $this->get($url);
                $this->assertTrue($resp->getStatusCode() == 200);
                //Trả lại
                $guest->removePermissionRouteNameOnUser($rn);
            }
        }

    }
}
