<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class HrEmployee extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    //    protected $dateFormat = 'd-m-Y';

    public static $hrAllUserTimeSheet = [];

    public static function getDataHtmlToPrint($mId)
    {
    }

    public function getValidateRuleInsert()
    {
        return [
            'user_id' => 'required|integer|unique:'.$this->getTable(),
            'first_name' => 'sometimes|string|min:1|max:100',
            'phone_number' => 'sometimes|numeric|digits_between:10,11',
            'last_name' => 'sometimes|min:1|max:100|nullable',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        $mm = $this->getValidateRuleInsert();
        $mm['user_id'] = 'required|integer|unique:'.$this->getTable().",user_id,$id";
        //'username'=>'sometimes|required|regex:/\w*$/|alpha_dash|regex:/\w*$/|max:50|min:6|unique:users,username,'.$id,

        return $mm;
    }

    public function getChucVu()
    {
        if ($this->job_title) {
            $mt = new HrEmployee_Meta();
            $m1 = $mt->_job_title($this, null, null);

            if (isset($m1[$this->job_title])) {
                return $m1[$this->job_title];
            }
        }

        return null;
    }

    public function getSex()
    {
        if ($this->sex) {
            $mt = new HrEmployee_Meta();
            $m1 = $mt->_sex($this, null, null);

            if (isset($m1[$this->sex])) {
                return $m1[$this->sex];
            }
        }

        return null;
    }

    public function getSalary()
    {

        if ($this->job_title == 4) {

        }

    }
}
