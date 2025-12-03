<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;



class TestCase1 extends TestCase
{
    public static $respondAfterLogin;
    //Token cho các request sau khi đã co withHeader('Authorization', 'Bearer '.$token)
    public $tk = '';

    public function withHeader(string $name, string $value)
    {
        if($name == 'Authorization'){
            if(str_starts_with($value, 'Bearer '))
                $this->tk = substr($value, 7);
        }
        return parent::withHeader($name, $value);
    }


    public function postCurl1($url, $data = [], $tk = null){
        $url = env('APP_URL').$url;
        $url = str_replace("//", '/', $url);

        if(!$tk)
            $tk = $this->tk;

        dump(" TK = $tk");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$tk,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new \retCurl($httpCode, $response);
    }

    public function getCurl1($url, $tk = null){
        if(!str_starts_with($url, 'http'))
            $url = env('APP_URL').$url;
//        $url = str_replace("//", '/', $url);

        if(!$tk)
            $tk = $this->tk;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$tk,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $error = curl_error($ch);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            throw new \Exception("$url, cURL error ({$errorCode}): {$error}");
        }

        return new \retCurl($httpCode, $response);
    }

    public function tesLoginTrueAccount3()
    {

        //Ham hàn vứt mẹ đi, chạy đéo gì mà khng ổn định, login xong rôồi, mà baáo khng có quyền vào  /
        //Đéo hiểu sao bọn nào làm ngu thật
        return;

        $url = request()->fullUrl();
                echo "<br/>\n BaseURl2 : $url";

        $pw = microtime(1);
        $pw = '11111111';
        $mail = '_test_glx123_@glx.com.vn';
        //        if(User::where("email", $mail)->first())

        User::where('email', $mail)->forceDelete();

        User::createAMemberForTest($mail, $pw);

//        die('ok111');

        //       $user = User::where("email", $mail)->first();
        //       $user->password = bcrypt($pw);
        //       $user->update();

        Session::start();
        $response = $this->followingRedirects()->call('POST', '/post-login', [
            'email' => $mail,
            'password' => $pw,
            '_token' => csrf_token(),
        ]);

        dump500($response->content(), 2000);

        sleep(1);
        $this->assertEquals(200, $response->getStatusCode());
//        dump('xxx = '.$response->original->name());
        //        $this->assertEquals('member.index', $response->original->name());

//        die();
        static::$respondAfterLogin = $response;
    }
}
