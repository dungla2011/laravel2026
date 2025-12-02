<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class HrMessageTask extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function getHtmlShowFileOfMessageInChatBox()
    {
        $retHtml = '';

        if ($this->image_list) {
            $mm = explode(',', $this->image_list);
            foreach ($mm as $fid) {
                if ($fid && $file = FileUpload::find($fid)) {
                    if ($file instanceof FileUpload);
                    $link = $file->getCloudLink();
                    $fname = $file->name;
                    if (strlen($file->name) > 50) {
                        $fname = substr($file->name, 0, 50).' ...';
                    }
                    if (str_starts_with($file->mime, 'image')) {
                        $retHtml .= "<a href='$link' target='_blank'> <img src='$link' style='width: 150px; margin: 3px'> <br> $fname </a> <br>";
                    } else {
                        $retHtml .= "<a href='$link' target='_blank'> <div style='color: dodgerblue; border: 1px dashed #ccc; width: 150px; margin: 3px; text-align: center; padding: 5px '>  $fname </div> </a><br>";
                    }
                }
            }
        }

        return $retHtml;
    }
}
