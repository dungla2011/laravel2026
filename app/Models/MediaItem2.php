<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaItem2 extends Model
{
    use HasFactory;
    use SoftDeletes; // Nếu bạn sử dụng SoftDeletes

    protected $table = 'media_items';


    /**
     * Lấy tất cả các folder của item này
     */
    public function folders(): BelongsToMany
    {
        // Chỉ định rõ tên khóa ngoại trong bảng pivot
        return $this->belongsToMany(MediaFolder2::class, 'media_items_folders', 'media_item_id', 'media_folder_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Lấy folder chính của item này
     */
    public function primaryFolder()
    {
        return $this->belongsToMany(MediaFolder2::class, 'media_items_folders', 'media_item_id', 'media_folder_id')
            ->withPivot('is_primary')
            ->wherePivot('is_primary', true)
            ->first();
    }
}
