<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends ModelGlxBase
{
    use HasFactory;

    protected $guarded = [];

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>]+)$/u';

        return [
            'name' => 'required|regex:'.$sreg.'|max:256',
            'summary' => 'nullable|regex:'.$sreg.'|max:2000',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getValidateRuleInsert();
    }
}
