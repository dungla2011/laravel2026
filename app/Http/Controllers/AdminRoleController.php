<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\DeleteModelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Collection\Collection;

class AdminRoleController extends Controller
{
    use DeleteModelTrait;

    private $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function index()
    {

        //        foreach ($m1 AS $obj){
        //            if($obj instanceof Permission);
        //            $m2 = $obj->permissionChilds()->get();
        //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //            print_r($m2);
        //            echo "</pre>";
        //            break;
        //        }
        //        dd($m1);

        $data = $this->role->latest()->paginate(20);

        return view('admin.role.index', compact('data'));
    }

    public function edit($id)
    {

        $data = $this->role->find($id);
        if ($data instanceof Role);

        $allPerOfRole1 = $data->permissions;

        if ($allPerOfRole1 instanceof Collection);
        $allPerOfRole = $allPerOfRole1->sortBy('prefix');

        //        dump($allPerOfRole);

        foreach ($allPerOfRole as $r1) {
            //            dump("$r1->prefix" );
        }

        //        dump($allPerOfRole);

        $permissionsParent = Permission::orderBy('prefix', 'asc')->where('parent_id', 0)->get();
        if ($data instanceof Role);

        return view('admin.role.edit', compact('data', 'permissionsParent', 'allPerOfRole'));
    }

    public function update(Request $request, $id)
    {
        try {

            //            $validated = $request->validated();
            DB::beginTransaction();

            $role = $this->role->find($id);

            if ($role instanceof Role);

            $mUpdate = [
                'display_name' => $request->display_name,
                'name' => $request->name,
            ];

            $role->update($mUpdate);

            DB::connection()->enableQueryLog();

            $role->permissions()->sync($request->route_name_code);

            if ($role instanceof Role);

            DB::commit();

            //            dd(DB::getQueryLog());

            //            return redirect()->route("admin.role.index");

        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();

            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        }

        return redirect()->route('admin.role.edit', ['id' => $id])->with('success', 'Thành công!');
        //        return redirect()->route("admin.role.index");
    }

    public function delete($id)
    {
        return $this->deleteModelTrait($id, $this->role);
    }

    public function create()
    {

        $permissionsParent = Permission::where('parent_id', 0)->get();

        return view('admin.role.add', compact('permissionsParent'));
    }

    public function store(Request $request)
    {

        //        dd($request->all());

        try {

            DB::beginTransaction();

            $role = $this->role->create([
                'display_name' => $request->display_name,
                'name' => $request->name,
            ]);

            if ($role instanceof Role);

            //            foreach ($request->role_id AS $roleItem){
            //                DB::table('role_user')->insert([
            //                   'role_id' => $roleItem,
            //                   'user_id' => $role->id,
            //                ]);
            //            }

            $role->permissions()->attach($request->route_name_code);

            DB::commit();

            //            $product->category();

            //        dd($product);

            //        return $path;
            return redirect()->route('admin.role.index');

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
