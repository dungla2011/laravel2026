<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class RoleUser extends ModelGlxBase
{
    use HasFactory, TraitModelExtra;

    protected $guarded = [];

    protected $table = 'role_user';
}
