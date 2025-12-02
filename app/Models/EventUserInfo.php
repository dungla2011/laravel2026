<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class EventUserInfo extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public $parent_text;
    public $org_text;

    function getFullname()
    {
        return "$this->last_name $this->first_name";
    }
    function getFullnameAndTitle()
    {
        return "$this->title $this->last_name $this->first_name";
    }

    public function getValidateRuleInsert()
    {
        return [
//            'email' => 'required|email|unique:event_user_infos,email',
            'email' => 'required|email|unique:event_user_infos,email,NULL,id,deleted_at,NULL',
        ];

    }

    public function getValidateRuleUpdate($id = null)
    {
        return [
            'email' => 'email|unique:event_user_infos,email,' . $id . ',id,deleted_at,NULL',
        ];

    }
}
