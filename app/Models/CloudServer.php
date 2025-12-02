<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class CloudServer extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;
    protected $guarded = [];

    static function getProxyDomainServer($domainServer)
    {
        return self::where('domain', $domainServer)->first()?->proxy_domain;
    }

    static function getServerDomainAndProxy()
    {
        return \App\Models\CloudServer::where("enable", 1)->pluck('proxy_domain', 'domain')->toArray();
    }


}
