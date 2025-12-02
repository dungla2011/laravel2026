<?php

namespace App\Models;
use Godruoyi\Snowflake\Snowflake;

trait SnowflakeId
{
    /**
     * Boot the trait
     */
    protected static function bootSnowflakeId()
    {
        // ⭐ CHỈ CHECK 1 LẦN DUY NHẤT
//        if(!SiteMng::isUsePosgresDb()){
//            return;
//        }

//        die("xxxxx121221");

        if(!SiteMng::use_snowflake_models(class_basename(static::class))){
            return ;
        }


        if(isDebugIp()){
//            die(" Cls = " . class_basename(static::class));
        }
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $new_id = \GlxSnowflake::id();
                $model->{$model->getKeyName()} = $new_id;
            }
        });

        static::saving(function ($model) {
            if (!$model->exists && empty($model->{$model->getKeyName()})) {
                $new_id = \GlxSnowflake::id();
                $model->{$model->getKeyName()} = $new_id;
            }
        });
    }

    public static function isValidSnowflakeId(string $id): bool
    {
        return strlen($id) >= 15 && strlen($id) <= 19 && ctype_digit($id);
    }

    // ⭐ laravel mặc định Tự động biết, nên ko cần hàm này?
    // public function getKeyType()
    // {
    //     // return SiteMng::use_snowflake_models(class_basename(static::class)) ? 'string' : 'int';
    //     return SiteMng::isUsePosgresDb() ? 'string' : 'int';
    // }

    public function getIncrementing()
    {
        return !SiteMng::use_snowflake_models(class_basename(static::class));
//        return !SiteMng::isUsePosgresDb(); // true cho MySQL, false cho PostgreSQL
    }

}
