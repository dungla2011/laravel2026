<?php

use Illuminate\Support\Facades\Cache;

?>

<div class="container" style="padding: 20px">
    <div style="background-color: lavender; padding: 10px; border-radius: 5px; border: 1px solid #ccc">
        <div style="margin: 10px auto 1px auto; max-width: 400px">
            <label style="" for="first_member_name_of_tree">
                <b>
                    &nbsp;Nhập tên thành viên đầu tiên để Tạo cây mới
                </b>
            </label>
            <div class="input-group mb-3 text-center">
                <input placeholder="Nhập tên thành viên" style="font-size: small" type="text"
                       id="first_member_name_of_tree" class="form-control">
                <div class="input-group-append">
                    <button data-code-pos='ppp17128872555171' id="btn_create_new_tree"
                            onclick="clsTreeTopDownCtrl.createNewTree('svg_grid')" class="btn btn-success"
                            type="button"
                            style="font-size: small">Tạo Cây
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div data-code-pos='ppp17166301798421' style="text-align: center; margin: 30px">
        <h2>
            Danh sách cây
        </h2>
    </div>
    <?php

    $objMeta = \App\Models\GiaPha::getMetaObj();

    $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

    $uid = getUserIdCurrent_();

    //Không phải mytree, thì list tất cả ra
    if ($domain == 'mytree.vn' || $domain == 'v3.mytree.vn' || $domain == 'v5.mytree.vn') {
        if (!$uid)
            goto _END2;
        $totalItem = \App\Models\GiaPha::where(['user_id' => $uid])->count();
        $mm0 = \App\Models\GiaPha::where(['user_id' => $uid, 'parent_id' => 0])->whereNull('married_with')->get();

    } else {
        $totalItem = \App\Models\GiaPha::count();
        $mm0 = \App\Models\GiaPha::where(['parent_id' => 0])->whereNull('married_with')->get();
    }

    if (!$mm0) {
        echo " <div style='text-align: center'> Bạn chưa có cây nào ! </div>";
    } else {
        $nTree = $mm0->count();
        if ($domain == 'mytree.vn' || $domain == 'v3.mytree.vn' || $domain == 'v5.mytree.vn') {


            $obj = new \App\Models\GiaPha();
            $mm = \App\Models\GiaPha::where(['user_id' => $uid, 'parent_id' => 0])->whereNull('married_with')->get();
            if (!$qt = \App\Models\GiaPhaUser::where('user_id', $uid)->first()) {
                \App\Models\GiaPhaUser::createQuotaUser($uid);
                if (!$qt = \App\Models\GiaPhaUser::where('user_id', $uid)->first()) {
                    bl('Error: Not found Quota user?');
                }
            }

            $nBuyed = \App\Models\GiaPhaUser::getCountBuyedNode($uid);
//
            {

            }

            echo " <div style='text-align: center; margin-bottom: 10px'> Có <b> $nTree </b> cây với tổng số <b> $totalItem </b> thành viên ";

            $padRedStyle = '';
            if($totalItem > $qt->max_quota_node + $nBuyed)
                $padRedStyle = ';color: red;';

            //                if(nowyh() > '2023-03-10')
            echo " (<span style='$padRedStyle'>giới hạn <b>".($qt->max_quota_node + $nBuyed)."</b>, nâng giới hạn <a href='/buy-vip'>Tại đây</a></span>) ";
            echo '</div>';
        }
        else
            $mm = \App\Models\GiaPha::where(['parent_id' => 0])->whereNull('married_with')->get();

        ?>
        <div class="card-columns">
            <?php
            $objMeta = \App\Models\GiaPha::getMetaObj();
            if ($objMeta instanceof \App\Models\GiaPha_Meta) ;
            foreach ($mm as $obj) {
                if ($obj->married_with) {
                    continue;
                }

                if ($obj instanceof \App\Models\GiaPha) ;

                //                    $nChild = 1;

                $keyCount = $objMeta->getCacheKeyCountTree(qqgetRandFromId_($obj->id));
                $model = new \App\Models\GiaPha();
                if (Cache::has($keyCount)) {
                    $nChild = Cache::get($keyCount);
                } else {
                    $nChild = $obj->countMember();
                    Cache::put($keyCount, $nChild, 30 * 24 * 60 * 60);
                }

                $rand = $obj->id;
//                if ($objMeta->isUseRandId())
                {
                    $rand = \App\Components\ClassRandId2::getRandFromId($obj->id);
                }

                //Tìm info
                $treeInfo = \App\Models\MyTreeInfo::where('tree_id', $obj->id)->first();
                ?>
                <div class='card text-center' style="">
                    <div class="card-body text-center" style="">
                        <a style="color: inherit; text-decoration: none;" href='<?php echo "/my-tree?pid=$rand" ?>'>
                            <img class="svg_icon_one_node" style="width: 25px" src="/assert/Ionicons/src/tree_9.svg"
                                 alt="">


                            <?php
                            if (isset($treeInfo) && $treeInfo && isset($treeInfo->name)) {
                                echo "<p class='card-text text2_green'><b> $treeInfo->name </b> <br> $treeInfo->title </p>";
                                echo '<hr>';
                            }
                            ?>
                        </a>
                        <p class='card-text'><b>
                                <a style="color: inherit; text-decoration: none;"
                                   href='<?php echo "/my-tree?pid=$rand" ?>'>
                                    <?php
                                    echo $obj->name
                                    ?>
                                </a>
                            </b> <br>
                            <i style="font-size: small"> <?php echo $nChild ?> thành viên </i>
                            <br>
                            <span style="font-size: small">
                                    Mã số cây (ID) : <?php echo $rand ?>
                                    </span>

                        </p>
                    </div>

                </div>
                <?php

                //                return;
            }
            ?>
        </div>
        <?php

    }


    _END2:
    ?>
</div>
