<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrSalaryMonthUser;

class HrSalaryMonthUserController extends BaseController
{
    protected HrSalaryMonthUser $data;

    public function __construct(HrSalaryMonthUser $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function report()
    {
        return view('admin.hr.report');
    }

    public function report2()
    {
        return view('admin.hr.report-salary-one-tree');
    }

    public function final_all_tree()
    {
        return view('admin.hr.report-salary-final-all-tree');
    }

    public function reportTimes()
    {
        return view('admin.hr.report-times');
    }
}
