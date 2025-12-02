<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class MediaFolder2 extends Model
{
    use HasFactory;
    use SoftDeletes; // Nếu bạn sử dụng SoftDeletes

    protected $table = 'media_folders';

    /**
     * Lấy tất cả các item trong folder này
     */
    public function items(): BelongsToMany
    {
        // Chỉ định rõ tên khóa ngoại trong bảng pivot
        return $this->belongsToMany(MediaItem2::class, 'media_items_folders', 'media_folder_id', 'media_item_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Lấy các item có folder này là folder chính
     */
    public function primaryItems()
    {
        return $this->belongsToMany(MediaItem2::class, 'media_items_folders', 'media_folder_id', 'media_item_id')
            ->withPivot('is_primary')
            ->wherePivot('is_primary', true);
    }
}
