<?php

namespace App\Models;

/**
 * Base trait for custom ID generation
 * Shared logic between SnowflakeId and UnixTimeId
 */
trait CustomIdTrait
{
    /**
     * Get the value indicating whether the IDs are incrementing
     * Check both Snowflake and UnixTime configurations
     * 
     * @return bool
     */
    public function getIncrementing()
    {
        $className = class_basename(static::class);
        
        // If using Snowflake or UnixTime, not auto-incrementing
        if (SiteMng::use_snowflake_models($className)) {
            return false;
        }
        
        if (SiteMng::use_unixtime_id_models($className)) {
            return false;
        }
        
        return true; // Default: auto-incrementing
    }
}
