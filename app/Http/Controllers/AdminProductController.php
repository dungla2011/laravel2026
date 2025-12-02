<?php

namespace App\Http\Controllers;

use App\Components\Recusive;
use App\Http\Requests\ProductAddRequest;
use App\Models\Category;
use App\Models\ProductBak;
use App\Models\ProductImage;
use App\Models\ProductTag;
use App\Models\Tag;
use App\Traits\DeleteModelTrait;
use App\Traits\StorageImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminProductController extends Controller
{
    use DeleteModelTrait;
    use StorageImageTrait;

    private $category;

    private $product;

    private $productImage;

    private $productTag;

    private $tag;

    public function __construct(Category $category,
        ProductBak $product,
        ProductImage $productImage,
        Tag $tag,
        ProductTag $productTag
    ) {
        $this->category = $category;
        $this->product = $product;
        $this->productImage = $productImage;
        $this->tag = $tag;
        $this->productTag = $productTag;
        parent::__construct();
    }

    public function index()
    {
        $data = $this->product->latest()->paginate(5);

        return view('admin.product.index', compact('data'));
    }

    public function list()
    {
        return response()->json([
            ProductBak::all(),
        ], 200);
    }

    public function getMenu($pid = null)
    {

        $data = $this->product::all();
        $recusive = new Recusive($data);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($data);
        //        echo "</pre>";

        return $recusive->MenuRecusive($pid);

    }

    public function edit($id)
    {

        //        dd(\request()->id);

        //        $prod = $this->product->find($id);
        //        dd($prod->productImages);
        //        //Tương tự:
        //        dd($prod->productImages()->get());
        //        //Lấy 1
        //        dd($prod->productImages()->first());

        $data = $this->product->find($id);

        //Áp dụng policy
        if (! auth()->user()->can('view', $data)) {
            //  abort(403);
        }

        //policy kiểu 2, không cần if
        //        $this->authorize('view', $data);

        $htmlOption = $this->getCategory($data->category_id);

        return view('admin.product.edit', compact('data', 'htmlOption'));
    }

    public function update(ProductAddRequest $request, $id)
    {

        try {

            //            $validated = $request->validated();

            DB::beginTransaction();

            $fileData = $request->file('feature_image_path');

            //            dd($fileData);

            $fileUpload = null;
            if ($fileData) {
                $fileUpload = $this->storeUploadFile($fileData, 'product');
            }

            $dataProduct = [
                'name' => $request->name,
                'price' => $request->price,
                'content' => $request->contents,
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,

            ];

            if ($fileData && $fileUpload) {
                $dataProduct['feature_image_name'] = $fileUpload['file_name'];
                $dataProduct['feature_image_path'] = $fileUpload['file_path'];
            }

            if ($this->product instanceof ProductBak);

            $this->product->find($id)->update($dataProduct);
            $product = $this->product->find($id);

            if ($product instanceof ProductBak);

            //        $this->product->save();

            if ($request->image_path) {
                //Update Xóa hết ảnh cũ đi
                //Cách này chưa ổn, đúng ra phải cho chọn ảnh trong thư viện...
                $this->productImage->where('product_id', $id)->delete();

                $fileDataS = $request->file('image_path');
                foreach ($fileDataS as $oneFileData) {
                    $fileUpload = $this->storeUploadFile($oneFileData, 'product');
                    if ($fileUpload) {

                        //                    $this->productImage->create([
                        //                        'product_id'=>$product->getKey(),
                        //                        'image_path' => $fileUpload['file_path'],
                        //                        'image_name' => $fileUpload['file_name'],
                        //                    ]);

                        //                        echo "<br/>\nxxx1";

                        //Hoặc - hasMany:
                        $product->images()->create([
                            'image_path' => $fileUpload['file_path'],
                            'image_name' => $fileUpload['file_name'],
                        ]);
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
                $product->tags()->sync($tagIds);
            }

            DB::commit();

            //            $product->category();

            //        dd($product);

            //        return $path;
            return redirect()->route('admin.product.index');

        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Có lỗi: '.$e->getMessage());
        }

        return redirect()->route('admin.product.index');
    }

    public function delete($id)
    {
        return $this->deleteModelTrait($id, $this->product);
    }

    public function search(Request $request)
    {
        $data = $this->product->getProductSearch($request);

        return view('admin.product.index', compact('data'));

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
        return view('admin.product.add', compact('htmlOption'));
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

    public function store(ProductAddRequest $request)
    {

        try {

            DB::beginTransaction();

            //        dd($request->file('image_path'));
            //        dd($request->tags);

            $fileData = $request->file('feature_image_path');

            $fileUpload = null;
            if ($fileData) {
                $fileUpload = $this->storeUploadFile($fileData, 'product');
            }

            $dataProduct = [
                'name' => $request->name,
                'price' => $request->price,
                'content' => $request->contents,
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,

            ];

            if ($fileData && $fileUpload) {
                $dataProduct['feature_image_name'] = $fileUpload['file_name'];
                $dataProduct['feature_image_path'] = $fileUpload['file_path'];
            }

            if ($this->product instanceof ProductBak);

            $product = $this->product->create($dataProduct);

            if ($product instanceof ProductBak);

            //        $this->product->save();

            if ($request->image_path) {
                $fileDataS = $request->file('image_path');
                foreach ($fileDataS as $oneFileData) {
                    $fileUpload = $this->storeUploadFile($oneFileData, 'product');
                    if ($fileUpload) {

                        //                    $this->productImage->create([
                        //                        'product_id'=>$product->getKey(),
                        //                        'image_path' => $fileUpload['file_path'],
                        //                        'image_name' => $fileUpload['file_name'],
                        //                    ]);

                        //                        echo "<br/>\nxxx1";

                        //Hoặc - hasMany:
                        $product->images()->create([
                            'image_path' => $fileUpload['file_path'],
                            'image_name' => $fileUpload['file_name'],
                        ]);
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
                $product->tags()->attach($tagIds);
            }

            DB::commit();

            //            $product->category();

            //        dd($product);

            //        return $path;
            return redirect()->route('admin.product.index');

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
