<?php

namespace App\Http\Controllers;

use App\Components\ClassMail1;
use App\Models\AffiliateLog;
use App\Models\LogUser;
use App\Models\Role;
use App\Models\SiteMng;
use App\Models\User;
use App\Support\HTMLPurifierSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use LadLib\Common\UrlHelper1;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{

    public function handleGoogleMobile(Request $request)
    {

        //Apache KHÔNG Cần đoan nay:
        //Cho pheép CORS
//        header("Access-Control-Allow-Origin: *");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header("Access-Control-Allow-Headers: Content-Type, Authorization");


//        if(isDebugIp())
//            die("1111");

        try {
            $idToken = $request->input('id_token');
            $accessToken = $request->input('access_token');
            $email = $request->input('email');
            $name = $request->input('name');

            if($ggClId = SiteMng::getGOOGLE_CLIENT_ID()){
                $client = new \Google_Client([
                    'client_id' => $ggClId
                ]);
            }
            else
                $client = new \Google_Client([
                    'client_id' => env('GOOGLE_CLIENT_ID', '211733424826-d7dns77hrghn70tugmlbo7p15ugfed4m.apps.googleusercontent.com')
                ]);

            $payload = null;

            // CÁCH 1: Ưu tiên id_token nếu có
            if ($idToken) {
                try {
                    $payload = $client->verifyIdToken($idToken);
                } catch (\Exception $e) {
                    \Log::error('verifyIdToken failed: ' . $e->getMessage());
                }
            }

            // CÁCH 2: Nếu không có id_token, dùng access_token verify qua Google API
            if (!$payload && $accessToken) {
                try {
                    $client->setAccessToken($accessToken);
                    $oauth2 = new \Google_Service_Oauth2($client);
                    $userInfo = $oauth2->userinfo->get();

                    $payload = [
                        'email' => $userInfo->email,
                        'name' => $userInfo->name,
                        'email_verified' => $userInfo->verifiedEmail,
                    ];
                } catch (\Exception $e) {
                    \Log::error('Google API verification failed: ' . $e->getMessage());
                }
            }

            // CÁCH 3: Nếu cả 2 đều fail, tin tưởng email từ Flutter (không khuyến khích)
            if (!$payload && $email) {
                // WARNING: Không an toàn, chỉ dùng cho dev/test
                $payload = [
                    'email' => $email,
                    'name' => $name,
                ];
            }

            if (!$payload || !isset($payload['email'])) {
                return response()->json([
                    'code' => 0,
                    'message' => 'Invalid Google authentication'
                ], 401);
            }

            // Verify email khớp
            if ($email && $payload['email'] !== $email) {
                return response()->json([
                    'code' => 0,
                    'message' => 'Email mismatch'
                ], 401);
            }

            $userEmail = $payload['email'];
            $userName = $payload['name'] ?? $name;

            // === PHẦN CODE TỒN TẠI/TẠO USER GIỮ NGUYÊN ===
            $objUser = User::where('email', $userEmail)->first();

            if (!$objUser) {
                // Check deleted user
                if ($objUser = User::withTrashed()->where('email', $userEmail)->first()) {
                    return response()->json([
                        'code' => 0,
                        'message' => 'User is deleted! Contact admin please!'
                    ], 403);
                }
            }

            if ($objUser) {
                // User đã tồn tại
                $objUser->setUserTokenIfEmpty();

                if (isEmailIsAutoSetAdmin($objUser->email)) {
                    if ($objUser->is_admin != 1) {
                        $objUser->is_admin = 1;
                        $objUser->update();
                    }
                    $objUser->_roles()->sync([1, 3]);
                }

                if (!$objUser->email_active_at) {
                    $objUser->email_active_at = now();
                    $objUser->update();
                }

            } else {
                // Tạo user mới
                $newUser = new User();
                $newUser->username = str_replace(['.', '@', '-'], '_', $userEmail);
                $newUser->email = $userEmail;
                $newUser->save();

                $newUser->setUserTokenIfEmpty();

                if (isEmailIsAutoSetAdmin($userEmail)) {
                    $newUser->is_admin = 1;
                }
                $newUser->email_active_at = now();
                $newUser->update();

                if (isEmailIsAutoSetAdmin($userEmail)) {
                    $obj = User::findUserWithEmail($userEmail);
                    $obj->_roles()->sync([1, 3]);
                } else {
                    $newUser->_roles()->sync([DEF_GID_ROLE_MEMBER]);
                }

                $objUser = $newUser;
            }

            // Return JWT token
            return response()->json([
                'code' => 1,
                'message' => 'Login successful',
                'payload' => $objUser->getJWTUserToken(), // JWT token
                'username' => $objUser->username,
                'email' => $objUser->email,
            ]);

        } catch (\Exception $e) {
            \Log::error('Google Mobile Login Error: ' . $e->getMessage());
            return response()->json([
                'code' => 0,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {


            $userGG = Socialite::driver('google')->user();
            $objUser = User::where('email', $userGG->email)->first();
            if (! $objUser) {
                if ($objUser = User::withTrashed()->where('email', $userGG->email)->first()) {
                    bl(__('login.user_deleted_cannot_register'));

                    return;
                }
            }

            if ($objUser) {
                $sid = getSiteIDByDomain();
                $objUser->setUserTokenIfEmpty();

                Auth::login($objUser);
                setcookie('_tglx863516839', $objUser->getJWTUserToken(), time() + 3600 * 24 * 180, '/');

                if (isEmailIsAutoSetAdmin($objUser->email)) {
                    if ($objUser->is_admin != 1) {
                        $objUser->is_admin = 1;
                        $objUser->update();
                    }
                    $objUser->_roles()->sync([1, 3]);
                    setcookie('_tglx__863516839', $objUser->getJWTUserToken(), time() + 3600 * 24 * 180, '/');
                }

                if ($objUser->is_admin) {
                    if(!setcookie('_tglx__863516839', $objUser->getJWTUserToken(), time() + 3600 * 24 * 180, '/')){
                    }
                }

                if (! $objUser->email_active_at) {
                    $objUser->email_active_at = nowyh();
                    $objUser->update();
                }

                //                bl3("Đăng nhập thành công!");
                //                echo "<br/>\n <a href='/member'> Member </a>";
                return redirect('/member');
                //                Auth::user()->_roles()->sync([DEF_GID_ROLE_MEMBER]);
            } else {

//                $newUser = User::create([
//                    //                    'username' => 'gg__'.$userGG->getId(),
//                    'username' => str_replace(['.', '@', '-'], '_', $userGG->email),
//                    'email' => $userGG->email,
//                    //                    'social_id'=> $user->id,
//                    //                    'social_type'=> 'google',
//                    //                    'password' => ''
//                ]);

                $newUser = new User();
                $newUser->username = str_replace(['.', '@', '-'], '_', $userGG->email);
                $newUser->email = $userGG->email;
//                $user->password = ($pr['password']);
//                $user->reg_str = $strActive;
                $newUser->save();

                $sid = getSiteIDByDomain();
//                $tokenUs = eth1b($sid.'-uid.'.$newUser->id.'-'.microtime().'-'.rand());
//                $newUser->token_user = $tokenUs;
                $newUser->setUserTokenIfEmpty();

                if (isEmailIsAutoSetAdmin($userGG->email)) {
                    $newUser->is_admin = 1;
                    setcookie('_tglx__863516839', $newUser->getJWTUserToken(), time() + 3600 * 24 * 180, '/');
                }
                $newUser->email_active_at = nowyh();
                $newUser->update();

                if (isEmailIsAutoSetAdmin($userGG->email)) {
                    $obj = User::findUserWithEmail($userGG->email);
                    $obj->_roles()->sync([1, 3]);
                }

                Auth::login($newUser);
                setcookie('_tglx863516839', Auth()->user()->getJWTUserToken(), time() + 3600 * 24 * 180, '/');
                Auth::user()->_roles()->sync([DEF_GID_ROLE_MEMBER]);
                tb3(__('login.register_success_short'), "<a href='/member'> " . __('login.continue') . "</a>");

                AffiliateLog::checkAffCode($newUser->id);
                //                return redirect('/home');
            }

        } catch (\Exception $e) {
            bl3(__('login.login_error'));
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
            if(isDebugIp()){
                echo "\n <hr> DEBUGIP <hr>";
                echo '<pre>';
                print_r($e->getTraceAsString());
                echo '</pre>';
            }
        }

    }

    public function ruleReg()
    {
        //            'title' => 'required|unique:posts|max:255',
        return [
            'username' => 'required|string|regex:/\w*$/|min:8|max:64|unique:users,username',
            'email' => 'required|email|unique:users|min:6|max:64',
            'password' => 'required|confirmed|min:8|max:64',
        ];
    }

    public function sendMailResetPw($email, $str)
    {

        $host = ucfirst(UrlHelper1::getDomainHostName());
        $urlBase = UrlHelper1::getUrlOrigin();
        $link = "$urlBase/reset-password-act?rs_string=$str";
        $cont = __('login.email_greeting') . "<br> " . __('login.email_reset_request') . "<br>" . __('login.email_reset_click') . " <a href='$link'> " . __('login.email_click_link') . " </a> <br>  " . __('login.email_or_copy') . ": $link <br>
" . __('login.email_reset_expires_15min') . "<br>
" . __('login.email_thank_you') . "<br>
$urlBase<br>
";

        return ClassMail1::sendMail('admin@glx.com.vn', "$host", $email, "$host - " . __('login.email_reset_password_subject'), "$cont");
    }

    public function sendMailActive($email, $strActive)
    {

        $host = ucfirst(UrlHelper1::getDomainHostName());
        $urlBase = UrlHelper1::getUrlOrigin();
        $link = "$urlBase/register?active=$strActive";
        $cont = __('login.email_greeting') . "<br>" . __('login.email_activation_instruction') . " <a href='$link'> " . __('login.email_click_link') . " </a> <br>  " . __('login.email_or_copy') . ": $link <br>
" . __('login.email_activation_expires_60min') . "<br>
" . __('login.email_thank_you') . "<br>
$urlBase<br>
";

        return ClassMail1::sendMail('admin@glx.com.vn', "$host", $email, "$host - " . __('login.email_activate_account_subject'), "$cont");
    }

    public function register(Request $request)
    {

        $pr = $request->all();



        if (isset($pr['active'])) {
            $str = $pr['active'];
            if (strlen($str) > 256 || ! preg_match('#^[a-zA-Z0-9]+$#', $str)) {

                LogUser::FInsertLog('Chuỗi kích hoạt không hợp lệ 1!');
                bl3(__('login.invalid_activation_string'), "<a href='/'> " . __('login.back_home') . "</a>");

                return;
            }
            $strActDecode = dfh1b($str);
            if (! strstr($strActDecode, '#')) {
                LogUser::FInsertLog('Chuỗi kích hoạt không hợp lệ 1!');
                bl3(__('login.invalid_activation_string'), "<a href='/'> " . __('login.back_home') . "</a>");

                return;
            }

            if (! $us = User::where('reg_str', $str)->first()) {
                LogUser::FInsertLog('Chuỗi kích hoạt không hợp lệ 1!');
                bl3(__('login.invalid_activation_string'), "<a href='/'> " . __('login.back_home') . "</a>");

                return;
            }

            if ($us->email_active_at) {
                $us->_roles()->sync([DEF_GID_ROLE_MEMBER]);
                LogUser::FInsertLog(null, null, 'Tài khoản đã kích hoạt thành công (1)', $us->id);
                tb3(__('login.account_activated_success'), "<a href='/login'> " . __('login.continue') . "</a>");

                return;
            }

            if (explode('#', $strActDecode)[1] < time() - 3600) {
                LogUser::FInsertLog(null, null, 'Quá hạn chuỗi kích hoạt', $us->id);

                bl3(__('login.activation_string_expired'), "<a href='/'> " . __('login.back_home') . "</a>");

                return;
            }

            $sid = getSiteIDByDomain();
            $tokenUs = eth1b($sid.'-uid.'.$us->id.'-'.microtime().'-'.rand());
            $us->token_user = $tokenUs;
            $us->email_active_at = nowyh();
            $us->log .= 'Active at: '.nowyh();
            if ($us->update()) {

                LogUser::FInsertLog(null, null, 'Kích hoạt thành công!', $us->id);

                tb3(__('login.account_activated_success'), "<a href='/login'> " . __('login.continue') . "</a>");
                //Cho quyền member:
                $us->_roles()->sync([DEF_GID_ROLE_MEMBER]);
            } else {
                LogUser::FInsertLog(null, null, 'Có lỗi kích hoạt!', $us->id);
                bl3(__('login.account_activation_error'), "<a href='/login'> " . __('login.continue') . "</a>");
            }

            return;
        }

        if (isset($pr['email']) && $request->isMethod('post')) {
            // Verify reCAPTCHA
            if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                return back()->withErrors(
                    ['recaptcha' => __('login.recaptcha_failed')]
                )->withInput();
            }

            unset($pr['_token']);

            $pr['username'] = trim(strtolower($pr['username']));
            $pr['email'] = trim(strtolower($pr['email']));

            if (! preg_match('/[a-zA-Z]/', $pr['username'][0])) {
                return back()->withErrors(
                    ['username' => __('login.username_must_start_with_letter')])->withInput();
            }

            if ($pr['password'] !== $pr['password2']) {
                return back()->withErrors(
                    ['password2' => __('login.password_mismatch')])->withInput();
            }

            //            if($rl = $us->getValidateRuleInsert()){
            //                $validator = \Illuminate\Support\Facades\Validator::make($pr, $rl);
            //                if ($validator->fails())
            //                {
            //                    $mE = $validator->errors()->all();
            //                    bl(implode("\n<br> - ", $mE));
            //                    return;
            //                }
            //            }

            $us = new User();
            $rl = $us->getValidateRuleInsert();


            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($pr);
//                echo "</pre>";
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($rl);
//                echo "</pre>";
//                die("IDx = xxx");
            }

            $validator = \Illuminate\Support\Facades\Validator::make($pr, $rl);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $email = trim($pr['email']);
            $strActive = eth1b($pr['email'].'#'.time());

            //            unset($pr['password2']);
            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($pr);
//                echo "</pre>";
//                die();
            }

//            $ret = User::create([
//                'username' => $pr['username'],
//                'email' => $pr['email'],
//                //Không cần encrypt ở đây vì tự động enc rồi:
//                'password' => trim($pr['password']),
//                'reg_str' => $strActive,
//            ]);

            $user = new User();
            $user->username = $pr['username'];
            $user->email = $pr['email'];
            $user->password = ($pr['password']);
            $user->reg_str = $strActive;
            $user->save();

            if ($pr['email'] == 'test002@gmail.com' || $this->sendMailActive($pr['email'], $strActive)) {
                LogUser::FInsertLog(null, null, "Send mail kích hoạt $strActive / ".$pr['email']);

                echo "<br/>\n";echo "<br/>\n";echo "<br/>\n";
                tb3(__('login.register_success'), __('login.email_sent_to') . ': '.$email);
            } else {
                LogUser::FInsertLog(null, null, "Không thể gửi mail Kích hoạt $email");
                echo "<br/>\n";echo "<br/>\n";echo "<br/>\n";
                bl3(__('login.send_activation_email_error'), " <a href='/active-account'> " . __('login.resend_activation_email') . " </a>");
            }

            return;
        }

        return $this->getViewLayout('login.register');

        return view('login.register');
    }

    public function resetPasswordAct(Request $request)
    {

        //        User::where("email",'dungla2011@gmail.com')->first()->update(['reset_pw'=>'36435f5269444546180207150700010302040e000106']);
        //

        if ($rss = $request->get('rs_string')) {

            $us = User::where('reset_pw', $rss)->first();
            if (! $us) {
                $str = ("Link đặt mật khẩu không hợp lệ: $rss");
                bl3($str, "<a href='/'> Trở lại</a>");

                return;
                //                return back()->withErrors([
                //                    'ok' => $str,
                //                ]);
            } else {
                $time = explode('#', dfh1b($rss))[1];
                //Link RS PW Tồn tại 15 phút
                if (! is_numeric($time) || $time < time() - 900) {
                    $str = ("Link đặt  mật khẩu không hợp lệ (hết hạn): $rss");
                    bl3($str, "<a href='/'> Trở lại</a>");

                    return;
                    //                    return back()->withErrors([
                    //                        'ok' => $str,
                    //                    ]);
                }
            }

            if ($request->isMethod('post')) {
                if ($request->get('password1') && $request->get('password2')) {
                    $pw1 = trim($request->get('password1'));
                    $pw2 = trim($request->get('password2'));
                    $str = 'Hai mật khẩu phải trùng nhau!';
                    if ($pw1 != $pw2) {
                        return back()->withErrors([
                            'password1' => $str,
                            'password2' => $str,
                        ]);
                    }
                    $rule = [
                        'password1' => 'required|min:8|max:64'];
                    $validator = \Illuminate\Support\Facades\Validator::make(['password1' => $pw1], $rule);
                    if ($validator->fails()) {
                        //dump($validator->getMessageBag());
                        return back()->withErrors($validator);
                    }
                    //Thực hiện reset pw:
                    $us->password = ($pw1);

                    $us->reset_pw = '';
                    $us->update();
                    $str = 'Đặt mật khẩu thành công!';

                    return redirect()->route('login.login')->withErrors([
                        'ok' => $str,
                    ]);
                }
            }
        }

        //        if($dbn = getDbNameWithDomain())
        //            return view("auth.$dbn.resetPasswordAct");
        return view('login.resetPasswordAct');
    }

    public function resetPassword(Request $request)
    {

        if ($request->isMethod('post') && $email = trim($request->get('email'))) {
            // Verify reCAPTCHA v3
            if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                return back()->withErrors([
                    'recaptcha' => __('auth.recaptcha_failed') ?? 'Xác thực reCAPTCHA thất bại. Vui lòng thử lại!'
                ])->withInput();
            }

            $rule = ['email' => 'required|email|min:6|max:64'];
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rule);
            if ($validator->fails()) {
                $str = (__('login.invalid_email') . ": $email");

                return back()->withErrors([
                    'email' => $str,
                ]);
            } else {
                $us = User::where('email', $email)->first();
                if (! $us) {
                    $str = (__('login.email_not_registered') . " ('$email'), " . __('login.can_register_with_this_email'));

                    return back()->withErrors([
                        'email' => $str,
                    ]);
                } else {
                    $us->reset_pw = eth1b('uid_rsp.'.$us->id.'#'.time());
                    $us->update();
                    ///reset-password-act?rs_string=384d515c674a4b48160c091b090e0f0d0c0a0a0c0908

                    if ($this->sendMailResetPw($email, $us->reset_pw)) {
                        tb3(__('login.reset_email_sent') . " $email. <br> " . __('login.check_email_for_reset') . " (" . __('login.check_spam') . ")", "<a href='/'> " . __('login.back_home') . " </a>");

                        return;
                    } else {
                        bl3(__('login.send_email_error'), " <a href='/'> " . __('login.back_home') . " </a>");

                        return;
                    }
                }
            }
        }

        return view('login.resetPassword');
    }

    public function activeAccount(Request $request)
    {

        if ($request->isMethod('post') && $email = trim($request->get('email'))) {

            // Verify reCAPTCHA v3
            if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                return back()->withErrors([
                    'recaptcha' => __('auth.recaptcha_failed') ?? 'Xác thực reCAPTCHA thất bại. Vui lòng thử lại!'
                ])->withInput();
            }

            $rule = ['email' => 'required|email|min:6|max:64'];

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rule);
            if ($validator->fails()) {
                $str = (__('login.invalid_email') . ": $email");

                return back()->withErrors([
                    'email' => $str,
                ]);
            } else {
                $us = User::where('email', $email)->first();
                if (! $us) {
                    $str = (__('login.email_not_registered') . " ('$email'), " . __('login.can_register_with_this_email'));

                    return back()->withErrors([
                        'email' => $str,
                    ]);
                } else {
                    if ($us->email_active_at) {
                        $str = (__('login.account_already_activated'));

                        return back()->withErrors([
                            'ok' => $str,
                        ]);
                    } else {

                        $strActive = eth1b($email.'#'.time());
                        $us->reg_str = $strActive;
                        $us->update();

                        //                        $strActive = $us->reg_str;
                        if ($email == 'test002@gmail.com' || $this->sendMailActive($email, $strActive)) {
                            $str = __('login.activation_email_sent') . ': '.$email.".\n " . __('login.check_email_for_activation') . " (" . __('login.check_spam') . ")";

                            return back()->withErrors([
                                'ok' => $str,
                            ]);
                        } else {
                            $str = (__('login.send_activation_email_error'));

                            return back()->withErrors([
                                'ok' => $str,
                            ]);
                        }
                    }
                }
            }
        }

        return view('login.activeAccount');
    }

    public function login(Request $request)
    {
        //Nếu có cookie thì tự động login:
        //Bỏ qua vì đã chạy trong RunBeforeAll
//        if (isset($_COOKIE['_tglx863516839'])) {
//            $user = User::where('token_user', $_COOKIE['_tglx863516839'])->first();
//            if ($user) {
//                Auth::login($user);
//            }
//        }


        //nếu đã login rồi thì chuyển về member
        if (Auth::check()) {
//            return redirect()->intended();
            return redirect()->route('member.index');
        }

        //        $layout_name = getLayoutName();
        //        if($layout_name){
        //            $vi = "public.$layout_name.index";
        //            if(view()->exists($vi))
        //                return view($vi);
        //        }

        return $this->getViewLayout('login.login');

        return view('login.login');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        setcookie('_tglx863516839', null, 0);
        setcookie('_tglx__863516839', null, 0);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function postLogin(Request $request)
    {
        //        dd($request->all());

//        die("Đã tắt chức năng đăng nhập!");
        $credentials = $request->validate([
            'email' => ['required', 'min:6', 'max:128'],
            'password' => ['required'],
        ]);

        // Verify reCAPTCHA v3
        if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'))) {
            return back()->withErrors([
                'recaptcha' => __('auth.recaptcha_failed') ?? 'Xác thực reCAPTCHA thất bại. Vui lòng thử lại!'
            ])->withInput();
        }

        if ($request->isMethod('post')) {
            if ($email = $request->get('email')) {
                $us = User::where('email', $email)->orWhere('username', $email)->first();
                if ($us) {
                    if(!$us->hasRole(DEF_GID_ROLE_MEMBER))
                    if (! $us->email_active_at) {
                        bl3(__('login.account_not_activated') . ": $email", "- " . __('login.check_email_for_activation_link') . " (" . __('login.check_spam') . ") <br>- <a href='/active-account'>" . __('login.activate_account_here') . "</a>");
                        return;
                    }

                    if(SiteMng::getAuthType() == 2){
                        $sha = sha1($request->password . $us->id);
                        $check = ($sha == $us->password);
                    }
                    else
                        $check = Hash::check($request->password, $us->password);

                    if ($check) {
                        Auth::login($us);

                        AffiliateLog::checkAffCode($us->id);

                        if (isEmailIsAutoSetAdmin($us->email)) {
                            if ($us->is_admin != 1) {
                                $us->is_admin = 1;
                                $us->update();
                            }
                            $us->_roles()->sync([1, 3]);
                        }

                        setcookie('_tglx863516839', $us->getJWTUserToken(), time() + 3600 * 24 * 180, '/');

                        if ($us->is_admin) {
                            setcookie('_tglx__863516839', $us->getJWTUserToken(), time() + 3600 * 24 * 180, '/');
                        }
                        usleep(10000);

                        return redirect()->route('member.index');
                    }
                }
            }
            return redirect()->route('login.login')->withErrors(__('login.login_failed'))->onlyInput('email');

        }

        //        return;
        //
        //        Auth::setUser();
        //
        //        $remember = $request->has('remember_me');
        //        if (Auth::attempt($credentials, $remember)) {
        //            $request->session()->regenerate();
        //            setcookie("_tglx863516839", Auth::user()->getUserToken() ,  time() + 3600 * 24 * 180, "/");
        //            return redirect()->route("member.index");
        ////            return redirect()->intended('/menu');
        //        }

        //        return back()->withErrors([
        //            'email' => 'The provided credentials do not match our records.',
        //        ])->onlyInput('email');

    }

    /**
     * Verify reCAPTCHA response
     *
     * @param string|null $recaptchaResponse
     * @return bool
     */
    private function verifyRecaptcha($recaptchaResponse)
    {
        if(isDebugIp()){
            return true;
        }

        if (empty($recaptchaResponse)) {
            \Illuminate\Support\Facades\Log::warning('reCAPTCHA response is empty');
            return false;
        }

        $secretKey = config('recaptcha.api_secret_key');

        if (empty($secretKey)) {
            \Illuminate\Support\Facades\Log::error('reCAPTCHA secret key not configured');
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip()
            ]);

            $result = $response->json();

            if (isset($result['success']) && $result['success'] === true) {
                // For reCAPTCHA v3, check score
                if (isset($result['score'])) {
                    $threshold = config('recaptcha.score_threshold', 0.5);
                    return $result['score'] >= $threshold;
                }
                return true;
            }

            \Illuminate\Support\Facades\Log::warning('reCAPTCHA verification failed', $result);
            return false;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            return false;
        }
    }
}
