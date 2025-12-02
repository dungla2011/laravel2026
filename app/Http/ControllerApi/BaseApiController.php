<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\EventFaceInfo;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\SiteMng;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use PHPUnit\Util\Exception;

/**
 * Lớp API Controller kế thừa cho mọi API Controller
 */
class BaseApiController extends BaseController
{
    /**
     * @var BaseRepositoryInterface
     */
    protected $data;

    public clsParamRequestEx $objParamEx;

    function __construct()
    {
        if(!SiteMng::getInstance())
            return;
        SiteMng::getInstance()->maintain_text = trim(SiteMng::getInstance()->maintain_text);
        if(SiteMng::getInstance()->maintain_text && strlen(SiteMng::getInstance()->maintain_text) > 10){
            $txt = SiteMng::getInstance()->maintain_text ;
            $txt = str_replace(["<br>", '<br/>'], "\n", $txt);
            echo strip_tags($txt);
            die();
        }
    }


    public function reLearn()
    {
        try {


            $image_list = \request('image_list');

            if (!$image_list) {
                return rtJsonApiError("Image list is required.");
            }



            try {
                $nFace = reLearnFace($image_list);
                return rtJsonApiDone("Số Face: $nFace", "Nhận dạng ảnh OK");
            }
            catch (\Throwable $exception) { // For PHP 7
                return rtJsonApiError("reLearn error: " . $exception->getMessage());
            }
            catch (\Exception $e){
                return rtJsonApiError("reLearn error: " . $e->getMessage());
            }

//        ob_clean();
//        echo file_get_contents($link);

//        return rtJsonApiDone($linkImgHex, "Nhận dạng ảnh OK");
        }
        catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("reLearn error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("reLearn error: " . $exception->getMessage());
        }
    }

    /**
     * List file...
     */

    public function list(): \Illuminate\Http\JsonResponse
    {
        try {
            $params = \request()->toArray();

            return $this->data->get_list($params, $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("list error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("list error: " . $exception->getMessage());
        }

    }

    /**
     * API search for Auto complete,
     * search LIKE field, return array  [value=>id , 'label'=>string, ...], for ex: { ['value'=><userid>, 'label'=><email>], ... }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            return $this->data->search($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("search error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("search error: " . $exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *                                       API:
     *
     * @apiParam new object
     */
    public function add(Request $request)
    {

        try {
            $input = $request->all();

            return $this->data->add($input, $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Add error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("Add error: " . $exception->getMessage());
        }
    }

    /**
     * Get Object Information
     */
    public function get($id)
    {
        try {
            return $this->data->get($id, $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Get error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("Get error: " . $exception->getMessage());
        }
    }

    //    function edit($id) {
    //        $data = $this->data->find($id);
    //        //return view('admin.demo.edit', compact('data'));
    //    }

    public function update($id, Request $request)
    {

        try {
            //\//ladDebug::addTime(" baseapi ", __LINE__);
            return $this->data->update($id, $request->all(), $this->objParamEx);
            //\//ladDebug::addTime(" baseapi ", __LINE__);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Update error1: " . $exception->getMessage() ." \n ".  $exception->getTraceAsString());
        }
        //\//ladDebug::addTime(" baseapi ", __LINE__);
    }

    public function update_multi(Request $request)
    {

        try {
            return $this->data->update_multi($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Update_multi error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("Update_multi error: " . $exception->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            return $this->data->delete($request, $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Delete error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("Delete error: " . $exception->getMessage());
        }
    }

    //    function update_parent(Request $request)
    //    {
    //        try {
    //            return $this->data->update_parent($request->all(), $this->objParamEx);
    //        } catch (\Throwable $exception) { // For PHP 7
    //            return rtJsonApiError($exception->getMessage());
    //        } catch (\Exception $exception) {
    //            return rtJsonApiError($exception->getMessage());
    //        }
    //    }

    public function un_delete(Request $request)
    {
        try {
            return $this->data->un_delete($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("Un_delete error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("Un_delete error: " . $exception->getMessage());
        }
    }

    public function tree_index(Request $request)
    {
        try {
            return $this->data->tree_index($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("tree_index error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("tree_index error: " . $exception->getMessage());
        }
    }

    public function tree_create(Request $request)
    {
        try {
            return $this->data->tree_create($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("tree_create error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("tree_create error: " . $exception->getMessage());
        }
    }

    public function tree_move(Request $request)
    {
        try {
            return $this->data->tree_move($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("tree_move error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("tree_move error: " . $exception->getMessage());
        }
    }

    public function tree_save(Request $request)
    {
        try {
            return $this->data->tree_save($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("tree_save error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("tree_save error: " . $exception->getMessage());
        }
    }

    public function tree_delete(Request $request)
    {
        try {
            return $this->data->tree_delete($request->all(), $this->objParamEx);
        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("tree_delete error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("tree_delete error: " . $exception->getMessage());
        }
    }
}
