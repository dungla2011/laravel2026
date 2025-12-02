<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use LadLib\Laravel\Database\TraitModelExtra;

class HrSessionType extends ModelGlxBase
{
    use HasFactory, TraitModelExtra;

    protected $guarded = [];

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$*<>]+)$/u';

        return [
            'name' => 'required|regex:'.$sreg.'|max:5',
            'desc' => 'nullable|regex:'.$sreg.'|max:200',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getValidateRuleInsert();
    }
}
