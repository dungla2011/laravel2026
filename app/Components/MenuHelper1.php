<?php

namespace App\Components;

class MenuHelper1
{
    public static function getMenuData($pid = 3)
    {
        $pr = ['pid' => $pid, 'get_all' => 1, 'order_by' => 'orders', 'order_type' => 'ASC'];
        $obj = new \App\Models\MenuTree();
        $ret = $obj->queryIndexTree($pr, new \App\Components\clsParamRequestEx());
        $mData = [];
        foreach ($ret[0] as $m1) {
            if (! $m1['gid_allow']) {
                continue;
            }
            $std = new \stdClass();
            $std->id = $m1['id'];
            $std->name = $m1['name'];
            $std->link = $m1['link'];
            $std->icon = $m1['icon'];
            $std->parent_id = $m1['parent_id'];
            $std->disable_href = 0;
            $mData[] = $std;
            //
        }
        $mData1 = unserialize(serialize($mData));

        return $mData1;
    }

    public static function show_menu_demo1_recursive_mobi(&$arrObj, $parent = 0, $level = '')
    {

        $strRet = '';
        if (count($arrObj) < 1) {

            return;
        }

        //
        //        $objMenu0 = new ModelMenuCms();
        //        $objMenu1 = new ModelMenuCms();

        //        $baseUrl = ClassRoute::getBaseUri();
        $baseUrl = '';

        if (! $level) {
            $level = 0;
        }
        $level++;
        $levelUl = $level + 1;

        //Duyet tat ca mang
        foreach ($arrObj as $key0 => $objMenu0) {
            if ($objMenu0->id == -1) {
                continue;
            }

            $countChild = 0;
            $id = $pid = $objMenu0->id;

            //echo "\n XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id , PInput = $parent)";
            //Neu co parent, thi xet
            if ($objMenu0->parent_id != $parent) {
                //  echo " ->bo qua";
                continue;
            }
            //echo "\n Tiep tuc XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id )";
            //Xem phan tu hien tai co child khong
            foreach ($arrObj as $key1 => $objMenu1) {
                if ($objMenu1->parent_id == $id && $objMenu1->id != $id) {
                    $countChild++;
                    break;
                }
            }

            $padIcon = '';
            //            if($objMenu0 instanceof ModelMenuCms);
            if ($objMenu0->icon) {
                $padIcon = "<i class='$objMenu0->icon'></i>";
            }
            // echo "<br/>$objMenu0->name : Co child";
            //Neu ko co con thi in ra luon
            if ($countChild == 0) {
                $padTs = '';
                //if(!$objMenu0->idnews)
                //  $padTs = "?ts=".time();
                $strRet .= "\n<li class=''> <a class=\"link_item_panel $level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon $objMenu0->name</a> \n</li> ";
                // echo "<br/>  $objMenu0->name  ( KO CO Child?) \n</li> ";
                $objMenu0->id = -1;

                continue;
            }

            //echo "<br/>  $objMenu0->name  ( CO Child?) \n</li> ";
            //Neu phan tu con co con
            //echo " <br>countChild = $countChild ";
            $padTs = '';
            //if(!$objMenu0->idnews)
            //  $padTs = "?ts=" + time();
            $padCaret = '';
            //if($level > 1)
            $padCaret = '<span class="sub-icon">+</span>';

            if ($objMenu0->disable_href > 0) {
                $strRet .= "\n<li class=''> <a class=\"link_item_panel $level\"> $padIcon  $objMenu0->name  </a> $padCaret";
            } else {
                $strRet .= "\n<li class=''> <a class=\"link_item_panel $level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon  $objMenu0->name  </a> $padCaret";
            }

            $strRet .= "\n<ul data-code-pos='qqq9084759485' class='level$levelUl'>";

            $parentNext = $objMenu0->id;
            $objMenu0->id = -1;
            //unset($arrObj[$key0]);

            $strRet .= static::show_menu_demo1_recursive_mobi($arrObj, $parentNext, $level);

            $strRet .= "\n</ul>";
            $strRet .= "\n</li>";
        }

        return $strRet;
    }

    public static function show_menu_demo1_recursive(&$arrObj, $parent = 0, $level = '')
    {

        $strRet = '';
        if (count($arrObj) < 1) {
            return;
        }

        //
        //        $objMenu0 = new ModelMenuCms();
        //        $objMenu1 = new ModelMenuCms();

        //        $baseUrl = ClassRoute::getBaseUri();
        $baseUrl = '';

        if (! $level) {
            $level = 0;
        }
        $level++;
        $levelUl = $level + 1;

        //Duyet tat ca mang
        foreach ($arrObj as $key0 => $objMenu0) {
            if ($objMenu0->id == -1) {
                continue;
            }

            $countChild = 0;
            $id = $pid = $objMenu0->id;

            //echo "\n XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id , PInput = $parent)";
            //Neu co parent, thi xet
            if ($objMenu0->parent_id != $parent) {
                //  echo " ->bo qua";
                continue;
            }
            //echo "\n Tiep tuc XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id )";
            //Xem phan tu hien tai co child khong
            foreach ($arrObj as $key1 => $objMenu1) {
                if ($objMenu1->parent_id == $id && $objMenu1->id != $id) {
                    $countChild++;
                    break;
                }
            }

            $padIcon = '';
            //            if($objMenu0 instanceof ModelMenuCms);
            if ($objMenu0->icon) {
                $padIcon = "<i class='$objMenu0->icon'></i>";
            }
            // echo "<br/>$objMenu0->name : Co child";
            //Neu ko co con thi in ra luon
            if ($countChild == 0) {
                $padTs = '';
                //if(!$objMenu0->idnews)
                //  $padTs = "?ts=".time();
                $strRet .= "\n<li class=''> <a class=\"menu-link$level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon $objMenu0->name</a> \n</li> ";
                // echo "<br/>  $objMenu0->name  ( KO CO Child?) \n</li> ";
                $objMenu0->id = -1;

                continue;
            }

            //echo "<br/>  $objMenu0->name  ( CO Child?) \n</li> ";
            //Neu phan tu con co con
            //echo " <br>countChild = $countChild ";
            $padTs = '';
            //if(!$objMenu0->idnews)
            //  $padTs = "?ts=" + time();
            $padCaret = '';
            if ($level > 1) {
                $padCaret = '<i class="fa fa-caret-right"></i>';
            }

            if ($objMenu0->disable_href > 0) {
                $strRet .= "\n<li class=''> <a class=\"menu-link$level\"> $padIcon  $objMenu0->name  $padCaret </a> ";
            } else {
                $strRet .= "\n<li class=''> <a class=\"menu-link$level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon  $objMenu0->name  $padCaret </a> ";
            }

            $strRet .= "\n<ul data-code-pos='qqq9084759485' class='level$levelUl'>";

            $parentNext = $objMenu0->id;
            $objMenu0->id = -1;
            //unset($arrObj[$key0]);

            $strRet .= static::show_menu_demo1_recursive($arrObj, $parentNext, $level);

            $strRet .= "\n</ul>";
            $strRet .= "\n</li>";
        }

        return $strRet;
    }

    public static function show_menu_recursive2(&$arrObj, $parent = 0, $level = '')
    {

        $strRet = '';
        if (count($arrObj) < 1) {
            return;
        }

        //
        //        $objMenu0 = new ModelMenuCms();
        //        $objMenu1 = new ModelMenuCms();

        //        $baseUrl = ClassRoute::getBaseUri();
        $baseUrl = '';

        if (! $level) {
            $level = 0;
        }
        $level++;
        $levelUl = $level + 1;

        //Duyet tat ca mang
        foreach ($arrObj as $key0 => $objMenu0) {
            if ($objMenu0->id == -1) {
                continue;
            }

            $countChild = 0;
            $id = $pid = $objMenu0->id;

            //echo "\n XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id , PInput = $parent)";
            //Neu co parent, thi xet
            if ($objMenu0->parent_id != $parent) {
                //  echo " ->bo qua";
                continue;
            }
            //echo "\n Tiep tuc XET: $objMenu0->name (id =$id, p=$objMenu0->parent_id )";
            //Xem phan tu hien tai co child khong
            foreach ($arrObj as $key1 => $objMenu1) {
                if ($objMenu1->parent_id == $id && $objMenu1->id != $id) {
                    $countChild++;
                    break;
                }
            }

            $aClass = 'nav-link';
            $liClass = 'nav-item';
            if ($level > 1) {
                $aClass = 'dropdown-item';
                $liClass = '';
            }

            $padIcon = '';
            //            if($objMenu0 instanceof ModelMenuCms);
            if ($objMenu0->icon) {
                $padIcon = "<i class='$objMenu0->icon'></i>";
            }
            // echo "<br/>$objMenu0->name : Co child";
            //Neu ko co con thi in ra luon
            if ($countChild == 0) {
                $padTs = '';
                //if(!$objMenu0->idnews)
                //  $padTs = "?ts=".time();
                $strRet .= "\n\n<li class='$liClass $level'> <a class=\"$aClass $level \" href='$baseUrl$objMenu0->link$padTs'> $padIcon $objMenu0->name</a> \n</li> ";
                // echo "<br/>  $objMenu0->name  ( KO CO Child?) \n</li> ";
                $objMenu0->id = -1;

                continue;
            }

            //echo "<br/>  $objMenu0->name  ( CO Child?) \n</li> ";
            //Neu phan tu con co con
            //echo " <br>countChild = $countChild ";
            $padTs = '';
            //if(!$objMenu0->idnews)
            //  $padTs = "?ts=" + time();
            $padCaret = '';
            if ($level > 1) {
                $padCaret = '<i class="fa fa-caret-right"></i>';
            }

            if ($objMenu0->disable_href > 0) {
                $strRet .= "\n\n<li class='$liClass dropdown $level'> \n<a data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle $level \"> $padIcon  $objMenu0->name  $padCaret </a> ";
            } else {
                $strRet .= "\n\n<li class='$liClass dropdown $level '> \n<a data-bs-toggle=\"dropdown\" class=\"nav-link dropdown-toggle $level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon  $objMenu0->name  $padCaret </a> ";
            }

            $strRet .= "\n\t<ul data-code-pos='qqq90867759485' class='dropdown-menu '>";

            $parentNext = $objMenu0->id;
            $objMenu0->id = -1;
            //unset($arrObj[$key0]);

            $strRet .= static::show_menu_recursive2($arrObj, $parentNext, $level);

            $strRet .= "\n\t</ul>";

            $strRet .= "\n</li>";
        }

        return $strRet;
    }
}
