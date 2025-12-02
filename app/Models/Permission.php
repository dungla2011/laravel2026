<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends ModelGlxBase
{
    use HasFactory;

    protected $guarded = [];

    public function permissionChilds()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    //    function setPrimaryKey($key)
    //    {
    //        $this->primaryKey = $key;
    //    }
}
