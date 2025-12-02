<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Components\Recusive;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class CategoryController extends BaseController
{
    private $category;

    public function __construct(Category $category, clsParamRequestEx $objPrEx)
    {
        $this->category = $category;
        $this->objParamEx = $objPrEx;
    }

    public function create()
    {

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        $htmlOption = $this->getCategory(null);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($htmlOption);
        //        echo "</pre>";
        return view('admin.category.add', compact('htmlOption'));
    }

    public function index()
    {
        $categories = $this->category->latest()->paginate(5);

        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {

        if (! $request->name) {
            return rtJsonApiError('Need name to insert ...');
        }
        //        $request->dd();
        //
        //        dd();

        $this->category->create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index');
    }

    public function getCategory($pid = null)
    {

        $data = $this->category::all();
        $recusive = new Recusive($data);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        return $recusive->categoryRecusive($pid);

    }

    public function edit($id)
    {


        $category = $this->category->find($id);

        $htmlOption = $this->getCategory($category->parent_id);

        return view('admin.category.edit', compact('category', 'htmlOption'));
    }

    public function update($id, Request $request)
    {

        $this->category->find($id)->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index');
    }

    public function delete($id)
    {

        $this->category->find($id)->delete();

        return redirect()->route('admin.categories.index');
    }
}
