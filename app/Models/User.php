<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use LadLib\Laravel\Database\TraitModelExtra;
use Laravel\Sanctum\HasApiTokens;

class User2 extends UserGlx
{
    use HasFactory, SoftDeletes, TraitModelExtra, SnowflakeId;
}

/**
 * @mixin EloquentBuilder
 * @mixin QueryBuilder
 */
//class User extends Authenticatable
//Sử dụng UserGlx để có thể ghi ChangeLog:
class User extends UserGlx
{
    use HasFactory, SoftDeletes, TraitModelExtra, SnowflakeId;
//    protected $fillable = ['password'];

    //Token phan quyen ko dung cai nay van chay: use HasApiTokens;

    protected $guarded = [];
    //
    //    function getTable()
    //    {
    ////        return "users";
    //    }

    /**
     * Lấy validation rules cơ bản cho User
     *
     * @param int|null $id User ID (để exclude khi update)
     * @param bool $isInsert True nếu là insert, false nếu là update
     * @return array
     */
    protected function getBaseValidationRules($id = null, $isInsert = true)
    {

        $meta = new User_Meta();
        $language = $meta->_language(null, null, null);
        $language = array_keys($language);
        $language = array_filter($language);
        //Làm sao trường ngôn ngữ trong mảng $language


        // Base rules với unique constraints khác nhau cho insert/update
        $rules = [
            'email' => $isInsert
                ? 'sometimes|required|email:rfc,dns|max:100|min:6|unique:users'
                : 'sometimes|required|email:rfc,dns|max:100|min:6|unique:users,email,'.$id,
            'username' => $isInsert
                ? 'sometimes|required|regex:/\w*$/|alpha_dash|regex:/\w*$/|max:50|min:6|unique:users,username'
                : 'sometimes|required|regex:/\w*$/|alpha_dash|regex:/\w*$/|max:50|min:6|unique:users,username,'.$id,
            'password' => 'nullable|max:50|min:8',
        ];


        // Language rules từ Meta (có thể expand thêm trong tương lai)
        foreach ($language as $lang) {
            if (!$lang) {
                continue;
            }
            $rules['meta_language.' . $lang] = 'nullable|in:' . $lang;
        }

        // Validation cho trường language chính
        if (!empty($language)) {
            $validLanguages = implode(',', $language);
            $rules['language'] = 'nullable|in:' . $validLanguages;
        }

        return $rules;
    }

    public function getValidateRuleInsert()
    {
        return $this->getBaseValidationRules(null, true);
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getBaseValidationRules($id, false);
    }

    public static function getTokenByUserId($uid)
    {
        $user = User::where('id', $uid)->first();
        if (! $user) {
            return null;
        }

        return $user->getJWTUserToken();
    }

    function hasRole($roleId)
    {
        $mRoleId = $this->getRoleIdUser(1);
        if($mRoleId)
        if(in_array($roleId, $mRoleId))
            return true;
        return false;
    }

    public function setUserTokenIfEmpty()
    {
        return;
        if ($this->token_user) {
            return;
        }
        $sid = getSiteIDByDomain();
        $tokenUs = eth1b($sid.'-uid.'.$this->id.'-'.microtime().'-'.rand());
        $this->token_user = $tokenUs;
        $this->update();
    }

    function getRoleNames()
    {
        $roles = $this->_roles()->get();
        $ret = '';
        foreach ($roles as $role) {
            $ret .= $role->name.', ';
        }
        return trim($ret, ', ');
    }

    public function getJWTUserToken()
    {
        $payload = [
            'user_id' => $this->id,
            'exp' => time() + 60 * 60 * 24 * 180,
        ];
        return 'TK1_'.JWT::encode($payload, env('APP_KEY'), 'HS256');
    }

    public function getUserToken()
    {
        return $this->getJWTUserToken();
        //        if(!$this->token_user)
        //            loi("Not found user token!");
        return $this->token_user;
    }

    function getLanguage()
    {
        return $this->language ?? null;
    }

    public static function getTokenByEmail($email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return null;
        }

        return $user->getJWTUserToken();
    }

    function getNameTitle()
    {
        return $this->name ?? $this->email ?? $this->username;
    }

    public static function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return null;
        }

        return $user;
    }

    /**
     * @return User
     */
    public static function getUserByTokenAccess($tk = null)
    {
        //Token được gửi từ Header
        //Lấy Token từ DB, sau đó tìm ra userid,

        if ($tk) {
            $tk1 = $tk;
        }
        else
            $tk1 = trim(request()->bearerToken());

        if(!$tk1){
            //Cho cac phien ban cu:
            $tk1 = request()->header("accesstoken01");
//            die("TK1 = $tk1");
        }
        if(!$tk1){
            $tk1 = $_COOKIE['_tglx863516839'] ?? '';
        }

        //Nếu ko có hãy lấy BearerToken


        if(isDebugIp()){
//            die("TKx = $tk1");
        }

        //Neu token dang tk1_
        if(str_starts_with($tk1, 'TK1_')){
            $tk1 = substr($tk1, 4);
            $payloadTk = JWT::decode($tk1, new Key(env('APP_KEY'), 'HS256'));
//            $payloadTk = JWT::decode($tk1, env('APP_KEY'), 'HS256');

            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($payloadTk);
//                echo "</pre>";
//                die();
            }

            $userid = $payloadTk->user_id ?? null;
            $user = User::find($userid);
//            if(isDebugIp())
//            if($user){
//                die(" OK user ");
//            }

            return $user;
        }


        //get_headers();

        //
        //        die("TK1 = $tk1");

        if (! $tk1 || strlen($tk1) < 3) {
            return null;
        }
        //$tk1 = request()->header("token_user");
        //Lấy ra user với token nếu có

        //        DB::enableQueryLog();
        $user = User::where('token_user', $tk1)->first();
        //
        //        $qr = DB::getQueryLog();
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($qr);
        //        echo "</pre>";

        return $user;
    }

    /**
     * @return User|Model|object|null
     */
    public static function findUserWithEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public static function loginAndSetCookieToken($email)
    {
        if ($user = User::where('email', $email)->first()) {
            Auth::login($user);
            //            dump("Set cookie...");
            setcookie('_tglx863516839', $user->getJWTUserToken(), time() + 3600 * 24 * 180, '/');

            return $user;
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    //Bắt buộc phải Meta có cùng tên model User_Meta->_roles
    //thì sẽ hoạt động ok
    public function _roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Kiểm tra quyền của user trên route hiện tại
     *
     * @return bool
     */
    public static function checkPermissionRouteName($routeName)
    {



        //        dump(auth()->user()->email);
        //Nếu là API
        if (request()->is('api/*')) {


            $user = User::getUserByTokenAccess();

            /////////////////////////////////////////////////////
            //Nếu ko đúng token thì return luôn, ko check session
            if (DEF_DISABLE_SESSION_FOR_API) {
                if (! $user) {
                    return null;
                }
            }

            $tokenUser = request()->bearerToken();

            //            echo "<br/>\n Email: $user->email";

            if ($user) {

                //Chỉ khi user Token và session khác nhau mới cần login lại Sesion với user Token
                //nếu ko sẽ bị logout web
                //Cũng có thể ko cần login webSession ở đây, nghĩa là bỏ đoạn này đi
                if (auth()->id() != $user->id) {
                    //Với API token, thiết lập user logined ở đây để báo lỗi 403 có thể nhận ra user đang login, Nếu ko sẽ nhận là Guest
                    //Login này sẽ xóa session cũ nếu có
                    Auth::login($user);
                }

            } else {
                //Nếu ko có token, vẫn có thể dựa trên Web Url, check session
                //Muốn check session ở đây, thì trong RouteServiceProvider::boot() , với api phải đặt ->middleware('web')
                //Với API, nếu token rỗng, thì mới xác thực bằng session
                //Vì có trường hợp session được SET khi Token đã login thành công, sau đó đổi 1 token lỗi, thì vẫn nhận session user cũ, là sai logic)
                if (! $tokenUser) {
                    if ($user = auth()->user()) {
                    } else {
                        return false;
                    }
                }
            }

        } //Nếu là Web, sẽ dùng session
        else {
            //            die("xxx");
            $user = auth()->user();
        }

        if ($user instanceof User);

        if (! $user) {
            return false;
        }

        //1. Lấy ra các role của user
        $roles = $user->_roles()->get();

        foreach ($roles as $role) {
            if ($role instanceof Role);
            //2. Từng role, lấy ra list permission của role đó, so sánh
            if ($role->permissions->contains('route_name_code', $routeName)) {
                return true;
            }
            //            dump($role->permissions);
        }

        return false;
    }

    public function getAllRouteNameAllowThisUserAndUrl()
    {
        $roles = $this->_roles()->get();
        $mRouteAllowUser = [];
        foreach ($roles as $role) {
            if ($role instanceof Role);
            $pers = ($role->permissions()->get());
            foreach ($pers as $per) {
                if ($per instanceof Permission);
                $mRouteAllowUser[$per->url] = $per->route_name_code;
            }
        }

        return $mRouteAllowUser;
    }

    public function getAllRouteNameAllowThisUser()
    {
        $roles = $this->_roles()->get();
        $mRouteAllowUser = [];
        foreach ($roles as $role) {
            if ($role instanceof Role);
            $pers = ($role->permissions()->get());
            foreach ($pers as $per) {
                if ($per instanceof Permission);
                $mRouteAllowUser[] = $per->route_name_code;
            }
        }

        return $mRouteAllowUser;
    }

    public function removeAllPermissionOnUser()
    {
        $roles = $this->_roles()->get();
        //Tìm tất cả role, sau đó tìm pers...
        foreach ($roles as $role) {
            if ($role instanceof Role);
            $role->permissions()->sync([]);
        }
    }

    //Kiểm tra guest có quyền này không:
    //echo \App\Models\User::checkGuestPermissionRoute('api.member-tree-mng.tree-index');
    public static function checkGuestPermissionRoute($routeName)
    {
        return DB::table('permission_role')->where(['role_id' => 0, 'permission_id' => trim($routeName)])->count();
    }

    public function removePermissionRouteNameOnUser($routeName)
    {
        if (! \Illuminate\Support\Facades\Route::has($routeName)) {
            dd("Not found route name: $routeName");
        }
        $roles = $this->_roles()->get();
        //Tìm tất cả role, sau đó tìm pers...
        foreach ($roles as $role) {
            if ($role instanceof Role);
            $mRouteAllowUser = [];
            $pers = ($role->permissions()->get());
            //Tìm các permission,
            $haveRouteName = 0;
            foreach ($pers as $per) {
                if ($per instanceof Permission);
                //Nếu có 1 route đã tồn tại thì xóa đi để sync sau
                if ($per->route_name_code != $routeName) {
                    $mRouteAllowUser[] = $per->route_name_code;
                } else {
                    $haveRouteName = 1;
                }
            }
            if ($haveRouteName) {
                $role->permissions()->sync($mRouteAllowUser);
            }
        }
    }

    static function addUserAndPassword($email, $username, $password, $role = DEF_GID_ROLE_MEMBER) {
        $u = \App\Models\User::where('email', $email)->first();
        if(!$u){
            $u = new User();
            $u->username = $username;
            $u->password = $password;
            $u->email = $email;


            $u->save();
            $u->setRoleUserIfRoleNull($role);
        }
    }

    public function addAllowPermissionRouteNameOnUser($routeName)
    {
        if (! \Illuminate\Support\Facades\Route::has($routeName)) {
            dd("Not found route name: $routeName");
        }

        //Kiểm tra all Role của user có route này chưa:
        $mRouteAllowUser0 = $this->getAllRouteNameAllowThisUser();
        //Đã có route :
        if (in_array($routeName, $mRouteAllowUser0)) {
            return 1;
        }

        //Nếu chưa có thì thêm  route này vào role đầu tiên:
        //Tìm Role đầu tiên của user:
        $role = $this->_roles()->get()[0] ?? '';
        if (! $role) {
            return 0;
        }
        if ($role instanceof Role);

        $mRouteAllowUser = [];
        $pers = ($role->permissions()->get());
        //lấy ra all route đã có
        foreach ($pers as $per) {
            if ($per instanceof Permission);
            //Nếu có 1 route đã tồn tại thì xóa đi để sync sau
            $mRouteAllowUser[] = $per->route_name_code;
        }
        // nếu ko có route này trong mảng, thì thêm vào
        if (! in_array($routeName, $mRouteAllowUser)) {
            //            dump("Not have route: $routeName");
            $mRouteAllowUser[] = $routeName;
            //và đồng bộ:
            $role->permissions()->sync($mRouteAllowUser);

            return 1;
        } else {
            //            dump("++ Have route: $routeName");
        }

        return 0;
    }

    function hasRoleId($roleId)
    {
        $mRoleId = $this->getRoleIdUser(1);
        if($mRoleId)
        if(in_array($roleId, $mRoleId))
            return true;

        return false;
    }

    /**
     * Trả lại list role của user, ngăn cách dấu , nếu có nhiều
     *
     * @param  int  $firstIdOnly
     * @return string|null
     */
    public function getRoleIdUser($getArray = 0)
    {
        $mm = $this->_roles->toArray();
        if (! $mm) {
            return null;
        }
        //        if($firstIdOnly)
        //            return $mm[0]['id'];
        $ret = '';
        $mR = [];
        foreach ($mm as $role) {
            $ret .= $role['id'].',';
            $mR[] = $role['id'];
        }
        if ($getArray) {
            return $mR;
        }

        return trim($ret, ',');
    }

    public static function createUserMemberForTest(){
        $user = \App\Models\User::where('email', 'member@abc.com')->first();
        if (! $user) {
            $user = new User();
            $m1['username'] = 'member';
            $m1['email'] = 'member@abc.com';
            $m1['email_active_at'] = nowyh();
            $m1['password'] = '';
//            $m1['token_user'] = bcrypt('admin123');
            $ret = $user->create($m1);
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $ret->id, 'role_id' => 3]);

            return $ret;
        }

        return $user;
    }


    public static function createUserAdminDefault()
    {
        $user = \App\Models\User::where('email', 'admin@abc.com')->first();
        if (! $user) {
            $user = new User();
            $m1['username'] = 'admin_abc_com';
//            $m1['id'] = time();
            $m1['is_admin'] = 1;
            $m1['email'] = 'admin@abc.com';
            $m1['email_active_at'] = nowyh();
            $m1['password'] = env('ADMIN_PW_TEST');
//            $m1['token_user'] = bcrypt('admin123');
            $ret = $user->create($m1);
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $ret->id, 'role_id' => 1]);

            return $ret;
        }

        return $user;
    }

    /**
     * @return User|Model|object|null
     */
    public static function createGuestForTest()
    {
        $user = \App\Models\User::where('email', '_guest_for_test@abc.com')->first();
        if (! $user) {
            $user = new User();
            $m1['username'] = 'guest_for_test_123456';
            $m1['email'] = '_guest_for_test@abc.com';
            $m1['password'] = env('ADMIN_PW_TEST');
//            $m1['token_user'] = bcrypt(microtime(1));
            $ret = $user->create($m1);
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $ret->id, 'role_id' => 0]);

            return $ret;
        } else {

            if (! \Illuminate\Support\Facades\DB::table('role_user')->where(['user_id' => $user->id, 'role_id' => 0])->count()) {
                \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => 0]);
            }
        }

        return $user;
    }

    public function setRoleUserIfRoleNull($roleId = DEF_GID_ROLE_MEMBER)
    {
        if (!\Illuminate\Support\Facades\DB::table('role_user')->where(['user_id' => $this->id, 'role_id' => $roleId])->count()) {
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $this->id, 'role_id' => $roleId]);
        }
    }

    public static function setRoleUser($uid, $roleId = DEF_GID_ROLE_MEMBER)
    {
        if (! \Illuminate\Support\Facades\DB::table('role_user')->where(['user_id' => $uid, 'role_id' => $roleId])->count()) {
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $uid, 'role_id' => $roleId]);
        }
    }

    public static function createAMemberForTest($email, $pw)
    {
        $user = \App\Models\User::where('username', basename($email))->first();
        if (! $user) {
            $user = new User();
            $m1['username'] = basename($email);
            $m1['email'] = $email;
            $m1['password'] = $pw;
//            $m1['token_user'] = bcrypt($pw);
            $m1['email_active_at'] = nowyh();

            $ret = $user->create($m1);
            \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $ret->id, 'role_id' => DEF_GID_ROLE_MEMBER]);

            return $ret;
        } else {
            $user->email_active_at = nowyh();
            $user->password = $pw;
            $user->update();
            if (! \Illuminate\Support\Facades\DB::table('role_user')->where(['user_id' => $user->id, 'role_id' => DEF_GID_ROLE_MEMBER])->count()) {
                \Illuminate\Support\Facades\DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => DEF_GID_ROLE_MEMBER]);
            }
        }

        return $user;
    }

    /**
     * Always encrypt the password when it is updated.
     *
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        if(SiteMng::getAuthTypeSha1())
            $this->attributes['password'] = sha1($value . $this->getId());
        else
            $this->attributes['password'] = bcrypt($value);
    }

    /*
     * Lấy token admin: khi admin login AS khác, token admin này vẫn giữ nguyên
     */
    public static function isSupperAdmin()
    {
        $user = User::getUserByTokenAccess($_COOKIE['_tglx__863516839'] ?? '');
        if ($user && $user->is_admin) {
            return $user->id;
        }
        return null;
    }



    public static function isAdminLrv_()
    {
        $user = User::getUserByTokenAccess($_COOKIE['_tglx863516839'] ?? '');
        if ($user && $user->is_admin) {
            return $user->id;
        }
        return null;

    }

    public static function isSupperAdminDevCookie()
    {
        if ($user = User::getUserByTokenAccess($_COOKIE['_tglx__863516839'] ?? '')) {
            if (in_array($user->email, explode(',', env('AUTO_SET_DEV_ADMIN_EMAIL')))) {
                return $user->id;
            }
        }
        return null;
    }

    public static function isSupperAdminDbMaxtrixCookie()
    {
        if ($user = User::getUserByTokenAccess($_COOKIE['_tglx__863516839'] ?? '')) {
            if ($user->is_admin && in_array($user->email, explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS')))) {
                return $user->id;
            }
        }

        return null;
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
