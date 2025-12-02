<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Traits\DeleteModelTrait;
use App\Traits\TraitControlerBaseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    use DeleteModelTrait, TraitControlerBaseApi;

    private $data;

    private $role;

    public function __construct(User $data, Role $role)
    {
        $this->data = $data;
        $this->role = $role;
        parent::__construct();
    }

    public function index()
    {
        $data = $this->data->latest()->paginate(5);

        return view('admin.user.index', compact('data'));
    }

    public function edit($id)
    {

        $data = $user = $this->data->find($id);

        if ($user instanceof User);

        $roleUser = $user->_roles;

        //        $htmlOption = $this->getCategory($data->category_id);
        $roles = $this->role->all();

        return view('admin.user.edit', compact('data', 'roles', 'roleUser'));
    }

    public function update(Request $request, $id)
    {
        try {



            //            $validated = $request->validated();
            DB::beginTransaction();
            $user = $this->data->find($id);
            if ($user instanceof User);
            $mUpdate = [
                'username' => $request->username,
                'email' => $request->email,
            ];
            if ($request->password) {
                $mUpdate['password'] = bcrypt($request->password);
            }

            $user->update($mUpdate);

            if ($user instanceof User);

            //            foreach ($request->role_id AS $roleItem){
            //                DB::table('role_user')->insert([
            //                   'role_id' => $roleItem,
            //                   'user_id' => $user->id,
            //                ]);
            //            }

            $user->_roles()->sync($request->role_id);

            DB::commit();

            return redirect()->route('admin.user.index');

        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();

            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        }

        return redirect()->route('admin.user.index');
    }

    public function delete($id)
    {
        return $this->deleteModelTrait($id, $this->user);
    }

    public function create()
    {
        $roles = $this->role->all();

        return view('admin.user.add', compact('roles'));
    }

    public function store(Request $request)
    {

        //        dd($request);

        try {

            DB::beginTransaction();

            $user = $this->data->create([
                'username' => $request->username,
                'email' => $request->email,
//                'token_user' => $request->token_user,
                'password' => bcrypt($request->password),
            ]);

            if ($user instanceof User);

            //            foreach ($request->role_id AS $roleItem){
            //                DB::table('role_user')->insert([
            //                   'role_id' => $roleItem,
            //                   'user_id' => $user->id,
            //                ]);
            //            }

            $user->_roles()->attach($request->role_id);

            DB::commit();

            //            $product->category();

            //        dd($product);

            //        return $path;
            return redirect()->route('admin.user.index');

        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        }
    }
}
