<?php

namespace App\Traits;

trait DeleteModelTrait
{
    public function deleteModelTrait($id, $model)
    {
        try {

            $model->find($id)->delete();

            //return redirect()->route("admin.product.index");
            return response()->json([
                'code' => 200,
                'message' => 'done',
            ], 200);

        } catch (\Throwable $exception) { // For PHP 7
            return response()->json([
                'code' => -100,
                'message' => 'Có lỗi 1: '.$exception->getMessage(),
            ], 500);
        } catch (\Exception $exception) {
            return response()->json([
                'code' => -101,
                'message' => 'Có lỗi 2: '.$exception->getMessage(),
            ], 500);
            //            echo "<br/>\n Error2: " . $exception->getMessage();
        }
    }
}
