<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Components\Recusive;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class MenuController extends BaseController
{
    private $Menu;

    public function __construct(Menu $Menu, clsParamRequestEx $objPrEx)
    {
        $this->Menu = $Menu;
        $this->objParamEx = $objPrEx;

    }

    public function create()
    {
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        $htmlOption = $this->getMenu(null);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($htmlOption);
        //        echo "</pre>";
        return view('admin.menu.add', compact('htmlOption'));
    }

    public function index()
    {
        $menu = $this->Menu->latest()->paginate(5);

        return view('admin.menu.index', compact('menu'));
    }

    public function store(Request $request, clsParamRequestEx $objPr)
    {

        if (! $request->name) {
            return rtJsonApiError('Need name to insert ...');
        }
        //        $request->dd();
        //
        //        dd();

        $this->Menu->create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.menu.index');
    }

    public function getMenu($pid = null)
    {

        $data = $this->Menu::all();
        $recusive = new Recusive($data);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        return $recusive->MenuRecusive($pid);

    }

    public function edit($id)
    {

        $menu = $this->Menu->find($id);

        $htmlOption = $this->getMenu($menu->parent_id);

        return view('admin.menu.edit', compact('menu', 'htmlOption'));
    }

    public function update($id, Request $request)
    {

        $this->Menu->find($id)->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.menu.index');
    }

    public function delete($id)
    {

        $this->Menu->find($id)->delete();

        return redirect()->route('admin.menu.index');
    }
}
