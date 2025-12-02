<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class Telesale extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function setStatusTele_ThongtinDonDaGuiDenShip()
    {
        $this->order_status = 2;
    }

    public function khongChoCapNhatTeleObj()
    {
        if ($this->order_status > 1) {
            return 1;
        }

        return 0;
    }
}
