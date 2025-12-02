<?php
if (!$uid = getCurrentUserId()) {
    bl("Bạn cần <a href='/login'> Đăng nhập </a> để mua gói VIP");
    return;
}

$pridE = $prid = request()->get('prid');
if (is_numeric($prid)) {
    die("Not valid pid");
}
$prid = qqgetIdFromRand_($prid);

$product = \App\Models\Product::find($prid);
if (!$product)
    die("Not found product Id!");

$price = $product->price;
$total_amountV = number_formatvn0($product->price);

$linkBK = "/buy-vip?prid=$pridE&vendor=bk";
$linkMomo = "/buy-vip?prid=$pridE&vendor=mm";

if (request('vendor') == 'bk') {
    clsBaoKim::buyVip(request()->all());
    return;
}
if (request('vendor') == 'mm') {
    clsMomo::buyVip($uid, $product, request()->all());
    return;
}

?>
<style>
    .vendor_select {
        display: inline-block;
        margin: 10px;
        padding: 10px;
        background-color: white;
        border-radius: 10px;
    }

    .vendor_select img {
        width: 150px;
        height: 150px;

        border-radius: 5px;
    }
</style>

<div class='jumbotron'
     style='max-width: 800px; margin: 20px auto; background-color: #eee; padding: 30px; border-radius: 10px;
     text-align: center'>

    <h5>
        Bạn đang chọn
    </h5>

    <h2 style=''>

        {{$product->name}}
    </h2>
    <h4>

        {{$total_amountV}} đồng
    </h4>

    <hr>
    <h3>
        Chọn kênh thanh toán:
    </h3>

    <div style="" class="vendor_select">
        <form method="post" action="{{$linkBK}}" id="form-action" onsubmit="">
            <input type="hidden" name="description" value="Mua gói download" readonly>
            <input type="hidden" name="customer_email" value="">
            <input type="hidden" id="customer_phone" name="customer_phone" value=''>
            <?php
            $time = time();
            $obj = $product;
            $price = number_formatvn0($obj->price);
            echo "<input type='hidden' id='input_prod_$obj->id' type='radio' name='mrc_order_id' value='$uid.$time-$obj->id'>";
            ?>
            <button style="border: 0px; background-color: white" type="submit" name="submit">
                <img src="/images/vnpay.png" alt="">
            </button>
        </form>
    </div>

    <div style="" class="vendor_select">
        <a href='{{$linkMomo}}'>
            <img src="/images/momo.png" alt="">
        </a>
    </div>

</div>


