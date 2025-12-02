<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\DemoSub1;
use App\Models\DemoTbl;
use App\Models\TagDemo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class DemoController extends BaseController
{
    private DemoTbl $data;

    private $tag;

    public function __construct(DemoTbl $data, TagDemo $tag, clsParamRequestEx $objPrEx)
    {
        $this->tag = $tag;
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function create()
    {

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($htmlOption);
        //        echo "</pre>";
        return view('admin.demo.add');
    }

    public function index()
    {
        $data = $this->data->latest()->paginate(5);

        if ($data instanceof LengthAwarePaginator);
        $items = $data->getCollection()->toArray();
        //        print_r($data)

        //        dump($data->total());

        //        dd($data);

        //        dd($data);

        return view('admin.demo.index', compact('data'));
    }

    public function store(Request $request)
    {

        //        dd($request->all());

        $m1 = $request->only(['number1', 'number2', 'string1', 'string2']);
        unset($m1['_token']);
        $data = $this->data->create($m1);

        //Add tags
        if ($request->tags) {
            foreach ($request->tags as $tagName) {
                $tagIns = $this->tag->firstOrCreate(['name' => $tagName]);
                //                $this->productTag->create([
                //                    'product_id' => $product->id,
                //                    'tag_id' => $tagIns->id,
                //                ]);
                //
                $tagIds[] = $tagIns->id;
            }
        }
        if (isset($tagIds)) {
            $data->joinTags()->attach($tagIds);
        }

        return redirect()->route('admin.demo.index');
    }

    public function edit($id)
    {

        $data = $this->data->find($id);

        return view('admin.demo.edit', compact('data'));
    }

    public function update($id, Request $request)
    {

        $data = $this->data->find($id);
        $m1 = $request->only(['number1', 'number2', 'string1', 'string2']);
        $data->update($m1);

        if ($data instanceof DemoTbl);
        //        $data->sub1()->sync($request->sub_value);

        //Tìm các sub trong db của data này
        //Nếu sub đó ko có trong request lên thì xóa
        foreach ($data->sub1 as $sub) {
            if ($sub instanceof DemoSub1);
            if (! in_array($sub->sub_val, $request->sub_value)) {
                $sub->delete();
            }
        }

        //Nếu sub request lên không có cái nào trong db thì tạo
        if ($request->sub_value) {
            foreach ($request->sub_value as $val) {
                if ($sub instanceof DemoSub1);
                if (! $data->sub1->contains('sub_val', $val)) {
                    $data->sub1()->create(['sub_val' => $val]);
                }
            }
        }

        //Add tags
        if ($request->tags) {
            foreach ($request->tags as $tagName) {
                $tagIns = $this->tag->firstOrCreate(['name' => $tagName]);
                //                $this->productTag->create([
                //                    'product_id' => $product->id,
                //                    'tag_id' => $tagIns->id,
                //                ]);
                //
                $tagIds[] = $tagIns->id;
            }
        }
        if (isset($tagIds)) {
            $data->joinTags()->sync($tagIds);
        }

        return redirect()->route('admin.demo.index');
    }

    public function delete($id)
    {

        $this->data->find($id)->delete();

        return redirect()->route('admin.demo.index');
    }
}
