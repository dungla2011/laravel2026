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

    function isHaveEmail()
    {
        $email = strtolower($this->email);
        if($email)
        if(!str_contains($email, 'khongcomail') && !str_contains($email, 'khongcoemail')){
            if(\clsValidate::isEmail($email))
                return $email;
        }
        return false;
    }

    function isHavePhone()
    {
        if($this->phone){
            $phone = self::fixPhoneNumber($this->phone);
            if($phone)
                return $phone;
        }
        return false;
    }

    static function fixPhoneNumber($phone)
    {
        //xoá tất cả ký tự không phải số (không phải từ 0-9)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = str_replace('+', '', $phone);

        if(strlen($phone) == 12)
            if (substr($phone, 0, 3) == '084') {
                return '0'.substr($phone, 3);
            }
//    if (substr($phone, 0, 3) == '+84') {
//        return '0'.substr($phone, 3);
//    }
        if(strlen($phone) == 11)
            if (substr($phone, 0, 2) == '84') {
                return '0'.substr($phone, 2);
            }
        return $phone;
    }

    public function getValidateRuleInsert()
    {
        return [
//            'email' => 'required|email|unique:event_user_infos,email',
            'email' => 'required|email|unique:event_user_infos,email,NULL,id,deleted_at,NULL',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]+$/',
        ];

    }

    public function getValidateRuleUpdate($id = null)
    {
        return [
            'email' => 'email|unique:event_user_infos,email,' . $id . ',id,deleted_at,NULL',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]+$/',
        ];

    }
}
