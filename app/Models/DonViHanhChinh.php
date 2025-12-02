<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class DonViHanhChinh extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->setConnection('mysql_for_common');
        parent::__construct($attributes);
    }
}
