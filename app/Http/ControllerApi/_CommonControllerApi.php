<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use Illuminate\Http\Request;

class _CommonControllerApi extends BaseApiController
{
    public function __construct(clsParamRequestEx $objPrEx)
    {
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    /**
     * Convert kebab-case to PascalCase
     * vps-plan -> VpsPlan
     */
    private function kebabToPascalCase($str)
    {
        $parts = explode('-', $str);
        $parts = array_map('ucfirst', $parts);
        return implode('', $parts);
    }

    /**
     * Get single item: _api/vps-plan/get/4
     */
    public function handleGet(Request $request, $model_name, $id)
    {
        $modelClass = $this->kebabToPascalCase($model_name);
        $fullModelClass = "App\\Models\\{$modelClass}";

        if (!class_exists($fullModelClass)) {
            return rtJsonApiError("Not class!");
        }

        try {
            $item = $fullModelClass::findOrFail($id);
            return rtJsonApiDone($item);
        } catch (\Exception $e) {
            return rtJsonApiError($e->getMessage());
        }
    }

    /**
     * Add/Create: _api/vps-plan/add
     */
    public function handleAdd(Request $request, $model_name)
    {
        $modelClass = $this->kebabToPascalCase($model_name);
        $fullModelClass = "App\\Models\\{$modelClass}";

        if (!class_exists($fullModelClass)) {
            return rtJsonApiError(" Not found model");
        }

        try {
            $item = $fullModelClass::create($request->all());
            return rtJsonApiDone($item->id, "Add OK $item->id");
        } catch (\Exception $e) {
           return rtJsonApiError($e->getMessage());
        }
    }

    /**
     * Update: _api/vps-plan/update/4
     */
    public function handleUpdate(Request $request, $model_name, $id)
    {
        $modelClass = $this->kebabToPascalCase($model_name);
        $fullModelClass = "App\\Models\\{$modelClass}";

        if (!class_exists($fullModelClass)) {
            return rtJsonApiError(" Not found model");
        }

        try {
            $item = $fullModelClass::findOrFail($id);
            $item->update($request->all());

            return rtJsonApiDone($item->id, "Update OK $item->id");
        } catch (\Exception $e) {
            return rtJsonApiError($e->getMessage());
        }
    }

    /**
     * Delete: _api/vps-plan/delete?id=4
     */
    public function handleDelete(Request $request, $model_name)
    {
        $modelClass = $this->kebabToPascalCase($model_name);
        $fullModelClass = "App\\Models\\{$modelClass}";
        $id = $request->get('id');

        if (!$id) {
            return response()->json(['error' => 'Missing id parameter'], 400);
        }

        if (!class_exists($fullModelClass)) {
            return rtJsonApiError(" Not found model");
        }

        try {
            $item = $fullModelClass::findOrFail($id);
            $item->delete();
            return rtJsonApiDone($id, "Add OK $id");
        } catch (\Exception $e) {
            return rtJsonApiError($e->getMessage());
        }
    }
}
