<?php

namespace App\Repositories;

use App\Components\clsParamRequestEx;

/**
 * Các RepoInterface sẽ có chung các hàm, nên sẽ kế thừa từ BaseRepositoryInterface này
 */
interface BaseRepositoryInterface
{
    public function get_list($params, clsParamRequestEx $objPr);

    public function get($param, clsParamRequestEx $objPr);

    public function add($param, clsParamRequestEx $objPr);

    public function update($id, $param, clsParamRequestEx $objPr);

    public function update_multi($param, clsParamRequestEx $objPr);

    public function delete($param, clsParamRequestEx $objPr);

    public function un_delete($param, clsParamRequestEx $objPr);

    public function search($param, clsParamRequestEx $objPr);

    public function tree_index($param, clsParamRequestEx $objPr);

    public function tree_create($param, clsParamRequestEx $objPr);

    public function tree_move($param, clsParamRequestEx $objPr);

    public function tree_save($param, clsParamRequestEx $objPr);

    public function tree_delete($param, clsParamRequestEx $objPr);
}
