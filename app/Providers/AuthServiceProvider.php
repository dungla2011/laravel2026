<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {


        $this->registerPolicies();

        //Đặt trước, sau đều được
        $this->allowAdminAllPermission();

        $this->defineAllGateAutomatic();

    }

    public function defineAllGateAutomatic()
    {

        //Neu La CLI, thi ko chay, ko can xac thuc
        if(isCli()){
            return;
        }

        if(isDebugPcCli() || isLocalHost()){

        }

        //Nếu truy cập vào / của web, thì ko cần check permision:
        if (request()->is('/')) {
            return;
        }


        //Lấy ra mọi per từ bảng per để add vào gate:
        foreach (Permission::all()->reverse() as $perm) {
            $routeName = $perm->route_name_code; //admin.demo.edit
            //Đặt User $user = null, để phòng trường hợp user ko login web, chưa có session, thì ko vào bên trong hàm được
            //Khi đó có thể check API token_user

            Gate::define($routeName, function (?User $user = null) use ($routeName) {
                //Khi user null, là ko có session, thì phải khai báo user mới
                //để có thể vào gọi hàm check thêm token_user (nếu là API...)

                //---Kiểm tra guest có thể truy cập:
                //Lấy tất cả per của GUEST, kiểm tra quyền trên route đang truy cập:

                if (! $user) {
                    $roleGuest = Role::where('id', DEF_GID_ROLE_GUEST)->first();
                    if (! $roleGuest) {
                        exit('Not found roleGuest?');
                    }
                    if ($permissions = $roleGuest->permissions) {
                        if ($permissions->contains('route_name_code', $routeName)) {
                            return true;
                        }
                    }

                    //Có thể đơn giản hơn = cách này:
                    //User::checkGuestPermissionRoute($routeName);
                } else {
                }

                if (! $user) {
                    $user = new User();
                }

                $ret = User::checkPermissionRouteName($routeName);
                if (! $ret) {

                    return false;
                }

                return true;

            });
        }
    }

    //Nếu là admin sẽ cấp mọi quyền, overwrite các gate khác
    public function allowAdminAllPermission()
    {

        //Chỗ này API bị set cookie khi login xong
        //Do đó khi token đổi, thì vẫn dùng cookie đó, nên quyền ko đổi
        //Vì vậy có Option xly riêng với Token, nếu Token thì ko dùng Session cookie

        Gate::before(function (?User $user = null) {
            //Check user by API token:

            //Nếu là API thì sẽ luôn check user token
            //để không lấy user trong session, cookie cũ
            if (request()->is('api/*'))

            //Hoặc mọi trường hợp đều dùng accessToken mà ko dùng session
            //Để session time có thể giảm về thấp
            //Nhưng sao xóa session file vẫn bị logout
//            if(1)
            {
                $user1 = new User();
                if ($user1 = User::getUserByTokenAccess()) {

                    //Chỉ khi user Token và session khác nhau mới cần login lại Sesion với user Token
                    //Cũng có thể ko cần login webSession ở đây, nghĩa là bỏ đoạn này đi
                    if ($user && $user->id != $user1->id) {
                        //Login này sẽ xóa session cũ nếu có
                        Auth::login($user1);
                    }
                    if (! $user) {
                        Auth::login($user1);
                        $user = $user1;
                    }

                } else {
                    //Nếu user đã login với Session ở đây
                    //Nếu Set user =  null, thì API ko dùng session cookie của user đã login
                    if ($user && DEF_DISABLE_SESSION_FOR_API) {
                        $user = null;
                    }
                }
            } else {
                //Ko phải api Nếu có cookie session, thì set token, để api có thể hoạt động
                //??????? có thể set ở đâu khác chỉ 1 lần sau khi login để tránh bị set nhiều lần ở đây?
            }

            //Nếu ko có session user, thì kiểm tra token:
            //Tại sao lại enable/disable ở đây?
            if(0)
            if (!$user && request()->is('api/*')) {

                //Nếu là api, thì get token
                $user = new User();
                $user = $user->getUserByTokenAccess();
                if($user){
                    //Với token, thiết lập user logined ở đây để hàm getGidCurrent có thể biết là user nào đã login (có lẽ dựa vào session)
                    //Lệnh này sẽ set session cho user
                    Auth::login($user);

                }

                if ($user && $user->is_admin){
                    return true;
                }
            }

            if (! request()->is('api/*')) {

                if ($user) {

                    //set _tglx863516839 để các API web có thể lấy ra sử dụng
                    if(!($_COOKIE['_tglx863516839'] ?? ''))
                        setcookie('_tglx863516839', $user->getJWTUserToken(), time() + 3600 * 24 * 180, '/');
                }
            }



            //Nếu có User, nghĩa là Session đã login
            if ($user && $user->is_admin) {

                //Kiểm tra admin session hay ko
                return true;
            }

            //Chú ý: không return false ở đây, kẻo mất quyền các User Role khác
            //return false;
        });
    }
}
