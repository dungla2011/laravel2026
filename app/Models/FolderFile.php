<?php

namespace App\Models;

use App\Components\TreeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FolderFile extends ModelGlxBase implements TreeInterface
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getParentClass(){
        return null;
    }

    public function getChildClass(){
        return FileUpload::class;
    }

    public function getParent($uid){

    }
    public function getChildren($uid){
        $pid = $this->getId();
        if(!$this->getId())
            $pid = 0;
        $m1 = $this->where("user_id", $uid)->where('parent_id', $pid)->orderBy('name', 'asc')->get();

//        echo "<br/>\n $pid / $uid";
//        dump($m1);

        $m1 = $m1->map(function ($folder) {
            $folder->isFile = 0;
            return $folder;
        });
        $childCls = $this->getChildClass();
        $m2 = $childCls::where("user_id", $uid)->where('parent_id', $pid)->orderBy('name', 'asc')->get();
        // Add isFile field for files
        $m2 = $m2->map(function ($file) {
            $file->isFile = 1;
            return $file;
        });
        return $m1->merge($m2);
    }

    public function isLeaf(){

    }

    public function getLinkPublic()
    {
        return "/member/file?seoby_s2=C&seby_s10=".$this->getId();
    }

    public function getLink1()
    {
        if(!$this->link1){
            $this->link1 = "ms".eth1b($this->getId());
            $this->save();
        }
        return $this->link1;
    }

    function getLink1Attrib1ute()
    {

        return $this->link1;

        $std = (object) $this->toArray();

//        if(isDebugIp()){
//            dump($this);
//            die();
//        }

        if($std->link1 ?? '')
             return $std->link1 ;
    }

//    public function link11() : Attribute
//    {
////        if(!$this->link1){
////            $this->link1 = eth1b($this->id);
////            $this->save();
////        }
//        return $this->link1;
//    }
}
