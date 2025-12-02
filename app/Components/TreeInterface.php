<?php

namespace App\Components;

use App\Models\FileUpload;

interface TreeInterface
{
    public function getParent($uid);
    public function getChildren($uid);
    public function isLeaf();
    public function getParentClass();
    public function getChildClass();
}
