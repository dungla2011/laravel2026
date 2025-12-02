<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class MyTreeInfo extends ModelGlxBase
{
    use HasFactory, TraitModelExtra; //SoftDeletes,

    protected $guarded = [];


    function getNodeWidth()
    {
        if(!$this->node_width)
            return 81;
        return $this->node_width;
    }
    function getNodeHeight()
    {
        if(!$this->node_height)
            return 132;
        return $this->node_height;
    }

    function getSpaceNodeX()
    {
        if(!$this->space_node_x)
            return 5;
        return $this->space_node_x;
    }
    function getSpaceNodeY()
    {
        if(!$this->space_node_y)
            return 50;
        return $this->space_node_y;
    }

    function getFontSizeNode()
    {
        if(!$this->font_size_node)
            return 12;
        return $this->font_size_node;
    }
//
    function initDefaultValue(){
        if(!$this->space_node_x)
            $this->space_node_x = 3;
    }
//        if(!$this->node_width)
//            $this->node_width = 81;
//        if(!$this->node_height)
//            $this->node_height = 132;
//        if(!$this->space_node_y)
//            $this->space_node_y = 50;
//        if(!$this->font_size_node)
//            $this->font_size_node = 13;
////        node_width
////    node_height
////    space_node_y
////    space_node_x
////    font_size_node
//    }

}
