<?php

namespace App\Http\Controllers;

use App\Models\SiteMng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        if(isSupperAdmin_()){
            setcookie('admin_glx', '1', time() + 3600 * 10000, '/');
        }
        //        if($dbname = getDbNameWithDomain())
        //            return view("member.$dbname.index");

        return $this->getViewLayout('member.index');
        //        return view("member.index");
    }

    function uploader()
    {
        return $this->getViewLayout();

    }

    public function setPassword(Request $request)
    {

        if ($request->isMethod('post')) {
            $pr = $request->all();
            //            dump($pr);
            if (! $user = Auth::user()) {
                exit('Not login?');
            }
            $validator = \Illuminate\Support\Facades\Validator::make($pr,
                [
                    'password' => 'required|min:6|max:64',
                    'password1' => 'required|min:8|max:64',
                    'password2' => 'required|min:8|max:64',
                ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if(SiteMng::getAuthType() == 2) {
                if($user->password !== sha1($request->password . $user->id)){
                    return back()->withErrors(['ok' => 'Mật khẩu cũ không đúng(1)!']);
                }
            }
            else
            if (! Hash::check($request->password, $user->password)) {
                return back()->withErrors(['ok' => 'Mật khẩu cũ không đúng(2)!']);
            }

            if ($request->password1 != $request->password2) {
                return back()->withErrors(['ok' => 'Hai mật khẩu mới không trùng nhau!']);
            }

            //Da co setAtt
            $user->password = $request->password1;


            $user->update();

            return back()->withErrors(['ok' => 'Đổi mật khẩu thành công!']);
        }

        return view('member.set-password');
    }
}
