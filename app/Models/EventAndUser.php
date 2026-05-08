<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class EventAndUser extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    //    public $user_event_id;

    public function getValidateRuleUpdate()
    {

        return $this->getValidateRuleInsert();
    }

    public function getValidateRuleInsert()
    {
//        if(trim($this->cc_email) == '') {
//            return [];
//        }
//        die(".... $this->cc_email");
        $this->cc_email = str_replace(" ", "", $this->cc_email);
        $this->cc_email = str_replace(" ", "", $this->cc_email);
        $this->cc_email = str_replace(" ", "", $this->cc_email);
        $this->cc_email = str_replace(" ", "", $this->cc_email);

        return [
            'cc_email' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));
                    foreach ($emails as $email) {
                        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail("Email '$email' không hợp lệ.");
                        }
                    }
                }
            ]
        ];
    }

}
