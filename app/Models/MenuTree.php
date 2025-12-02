<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;
use LadLib\Laravel\Database\TraitModelTree;

class MenuTree extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, TraitModelTree;

    protected $guarded = [];

    protected $casts = [
        'translations' => 'array',
    ];

    /**
     * Get translated name for current or specified locale
     * @param string|null $locale
     * @return string
     */
    public function getTranslatedName($locale = null)
    {
//        $locale = $locale ?? app()->getLocale();
        return $this->translations[$locale] ?? $this->name;
    }

    public function getName($lang = '')
    {
        return $this->name ?? '';
        return "ABC";
    }

    public function isHaveChild(&$mMenu)
    {
        foreach ($mMenu as $obj) {
            if ($obj->parent_id == $this->id) {
                return 1;
            }
        }

        return 0;
    }

    function getMultiLanguage()
    {
        $ml = $this->multi_language ?? '';
        echo "<br/>\n $ml";
    }

    static function showMenuPublicSandBox($pre = null, $after = null, $lang = null)
    {
        $mm = self::getMenuArrayPublic($lang);


        if($lang)
            $lang = "/$lang";
    // Helper function to build menu tree
    // Helper function to build menu tree



?>



    <?php
    \App\Models\BlockUi::showEditLink_("/admin/menu-tree/tree?pid=3&gid=0&open_all=1", 'edit menu', 'position: static!important; height: 40px');
    renderMenu($mm, $lang);
    ?>

        <?php
    }

    static function getMenuArrayPublic($lang = '')
    {
        $menu = new \App\Models\MenuTree();
        $mm = $menu->getAllTreeDeep(3);
        if(!$mm)
            return;
//        $user = getCurrentUserId(1);
//        if(isAdminCookie())
        {
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mm);
//            echo "</pre>";
//            die();
            foreach ($mm AS &$obj){
                if($obj['translations'] ?? '')
                if(is_array($obj['translations'])) {
                    $obj['name'] = $obj['translations'][$lang] ?? $obj['name'];
                }
            }
        }

        //Mảng mm biến thành mảng các obj
        $mm = array_map(function($v){
            return (object)$v;
        }, $mm);

        //Sắp xêếp lại theo thứ tự orders
        usort($mm, function($a, $b){
            return $a->orders <=> $b->orders;
        });

        //mảng mm bỏ đi các obj co status = 0
        $mm = array_filter($mm, function($v){
            return str_contains(','.$v->gid_allow.',', ',0,');
        });
        return $mm;
    }

    public static function showMenuAdminLte($pid)
    {
        //$mMenu = \App\Models\MenuTree::where('parent_id', 4)->get();
        $mMenu0 = \App\Models\MenuTree::orderBy('orders', 'ASC')->get();
        $gidCurrent = getGidCurrent_();
        $mGid = explode(',', $gidCurrent);
        //Nếu gid curent chứa gid allow thì ok
        $mMenu = [];
        foreach ($mMenu0 as $obj) {
            $obj->gid_allow = ",$obj->gid_allow,";
            foreach ($mGid as $gidC) {
                if (! $gidC) {
                    continue;
                }
                if (strstr($obj->gid_allow, ",$gidC,") !== false) {
                    $mMenu[] = $obj;
                    break;
                }
            }
            //            if(strstr($obj->gid_allow, ",$gidCurrent,") !== false){
            //                $mMenu[] = $obj;
            //            }
        }
        \App\Models\MenuTree::showMenuAdminLte0($pid, $mMenu, $gidCurrent);
    }

    public static function showMenuAdminLte0($pid, &$mMenu, &$gid)
    {
        $lang = getCurrentUserId(1)?->getLanguage();

        //Xem có con không, nếu có thì mới tiếp tục
        $haveChild = 0;
        $isInRoot = 0;
        foreach ($mMenu as $obj) {
            if ($obj->parent_id == $pid) {
                $haveChild = 1;
            }
            if ($obj->id == $pid) {
                if ($obj->parent_id == 0) {
                    $isInRoot = 1;
                }
            }
        }
        if (! $haveChild) {
            return;
        }
        if (! $isInRoot) {
            echo '<ul class="nav nav-treeview nav-sidebar nav-child-indent">';
        }
        foreach ($mMenu as $obj) {
            if($obj instanceof MenuTree);
            if ($obj->parent_id == $pid) {

                $tg = '';
                if ($obj->open_new_window) {
                    $tg = " target='_blank' ";
                }
                if (! $obj->icon) {
                    $obj->icon = 'far fa-circle nav-icon';
                }

                echo "<li id='_menu_$obj->link' class='nav-item'><a $tg href='$obj->link' class='text-sm nav-link'><i class='$obj->icon'></i><p>".($obj->getTranslatedName($lang));
                if ($obj->isHaveChild($mMenu)) {
                    echo "<i class='right fas fa-angle-left'></i>";
                }
                echo '</p></a>';
                self::showMenuAdminLte0($obj->id, $mMenu, $gid);
                echo '</li>';
            }
        }
        if (! $isInRoot) {
            echo '</ul>';
        }
    }

    public static function show_menu_recursive2024(&$arrObj, $parent = 0, $level = '')
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
            //            if($level > 1)
            $padCaret = "<i class=\"fa fa-caret-right\" style=''></i>";

            if ($objMenu0->disable_href > 0) {
                $strRet .= "\n\n<li class='have_child $liClass  $level'> \n<a  class=\" $level \"> $padIcon  $objMenu0->name  </a> $padCaret ";
            } else {
                $strRet .= "\n\n<li class='have_child $liClass  $level '> \n<a  class=\" $level\" href='$baseUrl$objMenu0->link$padTs'> $padIcon  $objMenu0->name  </a> $padCaret ";
            }

            $strRet .= "\n\t<ul data-code-pos='qqq9086456456' class='sub-menu'>";

            $parentNext = $objMenu0->id;
            $objMenu0->id = -1;
            //unset($arrObj[$key0]);

            $strRet .= static::show_menu_recursive2024($arrObj, $parentNext, $level);

            $strRet .= "\n\t</ul>";

            $strRet .= "\n</li>";
        }

        return $strRet;
    }

    public static function showMenuPublic2024($pid)
    {
        //$mMenu = \App\Models\MenuTree::where('parent_id', 4)->get();
        $mMenu0 = \App\Models\MenuTree::orderBy('orders', 'ASC')->get();
        $gidCurrent = getGidCurrent_();
        $mGid = explode(',', $gidCurrent);
        //Nếu gid curent chứa gid allow thì ok
        $mMenu = [];
        foreach ($mMenu0 as $obj) {
            $obj->gid_allow = ",$obj->gid_allow,";
            foreach ($mGid as $gidC) {
                if (! $gidC) {
                    continue;
                }
                if (strstr($obj->gid_allow, ",$gidC,") !== false) {
                    $mMenu[] = $obj;
                    break;
                }
            }
            //            if(strstr($obj->gid_allow, ",$gidCurrent,") !== false){
            //                $mMenu[] = $obj;
            //            }
        }
        \App\Models\MenuTree::showMenuPublic2024_0($pid, $mMenu, $gidCurrent);
    }

    public static function showMenuPublic2024_0($pid, &$mMenu, &$gid)
    {
        //Xem có con không, nếu có thì mới tiếp tục
        $haveChild = 0;
        $isInRoot = 0;
        foreach ($mMenu as $obj) {
            if ($obj->parent_id == $pid) {
                $haveChild = 1;
            }
            if ($obj->id == $pid) {
                if ($obj->parent_id == 0) {
                    $isInRoot = 1;
                }
            }
        }
        if (! $haveChild) {
            return;
        }
        if (! $isInRoot) {

        }
        foreach ($mMenu as $obj) {
            if ($obj instanceof MenuTree);

            if ($obj->parent_id == $pid) {

                $tg = '';
                if ($obj->open_new_window) {
                    $tg = " target='_blank' ";
                }
                if (! $obj->icon) {
                    //                    $obj->icon = 'far fa-circle nav-icon';
                }
                echo "\n<li class=''>\n<a $tg href='$obj->link' class=''><i class='$obj->icon'></i>".($obj->name);
                if ($obj->isHaveChild($mMenu)) {
                    echo "<i class='right fas fa-angle-left'></i>";
                }
                echo "</a>\n";
                if ($obj->isHaveChild($mMenu)) {
                    echo "\t\n<ul class='sub-menu'>";
                    self::showMenuPublic2024_0($obj->id, $mMenu, $gid);
                    echo "\n</ul>";
                }
                echo "\n</li>\n";
            }
        }
        if (! $isInRoot) {
            echo "</ul>\n";
        }
    }
}
