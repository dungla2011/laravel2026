<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use Illuminate\Http\Request;

class _CommonController extends BaseController
{
    // public clsParamRequestEx $objParamEx;

    public function __construct(clsParamRequestEx $objPrEx)
    {
        $this->objParamEx = $objPrEx;
    }

    /**
     * Handle action with default 'list'
     * Route: _admin/{model_name}
     * Example: _admin/vps-plan -> calls index()
     */
    public function handleActionDefault(Request $request, $model_name)
    {
        return $this->handleAction($request, $model_name, 'list');
    }

    /**
     * Handle action with ID in URL path
     * Route: _admin/{model_name}/{action}/{id}
     * Example: _admin/vps-plan/edit/4 -> calls edit(4)
     */
    public function handleActionWithId(Request $request, $model_name, $action, $id)
    {
        // Add id to request
        $request->merge(['id' => $id]);
        return $this->handleAction($request, $model_name, $action);
    }

    /**
     * Handle dynamic model actions
     * Route: _admin/{model_name}/{action}
     * Example: _admin/order-info/list, _admin/vps-plan/create
     */
    public function handleAction(Request $request, $model_name, $action)
    {
        // Convert kebab-case to PascalCase
        // order-info -> OrderInfo, vps-plan -> VpsPlan
        $modelClass = $this->kebabToPascalCase($model_name);

        // Convert action name to method
        $actionMethod = strtolower($action);

        // Check if model exists
        $fullModelClass = "App\\Models\\{$modelClass}";
        if (!class_exists($fullModelClass)) {
            abort(404, "Model not found: {$modelClass}");
        }

        // Set the model data for BaseController
        $this->data = new $fullModelClass();

        // Route to appropriate action using BaseController methods
        try {
            switch ($actionMethod) {
                case 'list':
                case 'index':
                    // Debug: check if VpsPlan_Meta exists and has required methods
                    $metaClass = "{$fullModelClass}_Meta";
                    if (!class_exists($metaClass)) {
                        return response("Meta class not found: {$metaClass}\nModel: {$fullModelClass}", 500);
                    }

                    $objMeta = $this->data::getMetaObj();
                    if (!$objMeta) {
                        return response("getMetaObj() returned null for {$fullModelClass}", 500);
                    }

                    $indexViewName = $objMeta->getIndexViewName(\request()->getRequestUri());


                    if (!$indexViewName) {
                        return response("getIndexViewName() returned empty for {$fullModelClass}\nURI: " . \request()->getRequestUri(), 500);
                    }

                    return $this->index();

                case 'create':
                    return $this->create();

                case 'edit':
                    $id = $request->get('id');
                    if (!$id) {
                        abort(400, 'Missing id parameter');
                    }
                    return $this->edit($id);

                case 'delete':
                case 'destroy':
                    $id = $request->get('id');
                    if (!$id) {
                        abort(400, 'Missing id parameter');
                    }
                    return $this->delete($request);

                case 'view':
                case 'show':
                    $id = $request->get('id');
                    if (!$id) {
                        abort(400, 'Missing id parameter');
                    }
                    return $this->edit($id);

                default:
                    abort(400, "Unknown action: {$action}");
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error("CommonController Error", [
                'model' => $modelClass,
                'action' => $actionMethod,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Show error in dev mode
            if (config('app.debug')) {
                return response("Error: " . $e->getMessage() . "\n\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() . "\n\n" . $e->getTraceAsString(), 500);
            }

            abort(500, "Internal server error");
        }
    }

    /**
     * Convert kebab-case to PascalCase
     * order-info -> OrderInfo
     * vps-plan -> VpsPlan
     */
    private function kebabToPascalCase($str)
    {
        $parts = explode('-', $str);
        $parts = array_map('ucfirst', $parts);
        return implode('', $parts);
    }
}
