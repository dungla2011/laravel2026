<?php

namespace App\Models;

trait UnixTimeId
{
    use CustomIdTrait;
    /**
     * Boot the trait
     */
    protected static function bootUnixTimeId()
    {
        if(!SiteMng::use_unixtime_id_models(class_basename(static::class))){
            return ;
        }

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::getIdUnixTime();
            }
        });

        static::saving(function ($model) {
            if (!$model->exists && empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::getIdUnixTime();
            }
        });
    }

    /**
     * Generate ID based on Unix time with microseconds
     * Formula: (microtime - 1768000000) * 1000
     *
     * @return int
     */
    public static function getIdUnixTime()
    {
        return round((microtime(true) - 1700000000) * 1000);
    }

    /**
     * Validate if ID is in valid UnixTime format
     *
     * @param string|int $id
     * @return bool
     */
    public static function isValidUnixTimeId($id): bool
    {
        // UnixTime ID should be numeric and positive
        return is_numeric($id) && $id > 0;
    }
}
