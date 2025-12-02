<?php

namespace App\Repositories;

use App\Components\clsParamRequestEx;
use Illuminate\Database\Eloquent\Model;
use LadLib\Common\Database\mongoDb;

/**
 * Các Repo sẽ có chung các hàm lấy data, chỉ khác nhau ở Model, nên có BaseRepo này
 */
class BaseRepositoryMg implements BaseRepositoryInterface
{
    /**
     * @var mongoDb
     *              Mỗi lớp kế thừa sẽ có kiểu model riêng
     */
    protected mongoDb $model;

    public function get_list($params, clsParamRequestEx $objParam)
    {
        //return 'abc123';
        return rtJsonApiDone('testing mongo...', null, 1);
    }

    public function get($id, clsParamRequestEx $objParam)
    {

    }

    public function search($param, clsParamRequestEx $objParam)
    {

    }

    public function update($id, $param, clsParamRequestEx $objParam)
    {
        if ($ret = $this->model->update($id, $param, $objParam)) {
            return rtJsonApiDone($ret, 'DONE update api mg1!');
        }

        return rtJsonApiError('Error update?');
    }

    public function add($param, clsParamRequestEx $objParam)
    {

        $id = $this->model->insert($param, $objParam);

        return rtJsonApiDone($id, 'DONE add api mg!');
    }

    public function update_multi($param, clsParamRequestEx $objParam)
    {
        return $this->model->update_multi($param, $objParam);
    }

    public function delete($param, clsParamRequestEx $objParam)
    {
        try {
            $this->model->delete($param, $objParam);
        } catch (\Throwable $e) { // For PHP 7
            return rtJsonApiError($e->getMessage());
        } catch (\Exception $e) {
            return rtJsonApiError($e->getMessage());
        }

        return rtJsonApiDone('Done delete!');
    }

    public function un_delete($param, clsParamRequestEx $objParam)
    {

    }

    public function tree_save($param, clsParamRequestEx $objPr)
    {
        // TODO: Implement tree_save() method.
    }

    public function tree_move($param, clsParamRequestEx $objPr)
    {
        // TODO: Implement tree_move() method.
    }

    public function tree_create($param, clsParamRequestEx $objPr)
    {
        // TODO: Implement tree_create() method.
    }

    public function tree_delete($param, clsParamRequestEx $objPr)
    {
        // TODO: Implement tree_delete() method.
    }

    public function tree_index($param, clsParamRequestEx $objPr)
    {

        return rtJsonApiDone(' testing mg');
        // TODO: Implement tree_index() method.
    }
}
