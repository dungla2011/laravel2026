<?php


namespace Tests\Feature;

defined("PRICE_SAMPLE_GLX") || define("PRICE_SAMPLE_GLX", 1110);

use App\Models\DemoTbl;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\FolderFile;
use App\Models\OrderInfo;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\Sku;
use App\Models\Tag;
use App\Models\TagDemo;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;




/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class Product1Test extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }



    function delDataTmp()
    {
//        global $tmpProName, $tmpSku, $tmpSkuVal;
        $tmpProName =  "test_new_product_demo_glx";
        $tmpSku = "test_new_sku_demo_glx";
        $tmpSkuVal = "sku.val.glx";
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        ProductVariant::withTrashed()->forceDelete();
        ProductVariantOption::where('name', 'LIKE' , "$tmpSkuVal%")->forceDelete();
//        Product::withTrashed()->forceDelete();
//        Sku::withTrashed()->forceDelete();
        Product::withTrashed()->where('name', 'LIKE' , 'test_new_product_demo_glx.%')->forceDelete();
        Sku::withTrashed()->where('sku', 'LIKE' , "$tmpSku%")->forceDelete();
        Sku::withTrashed()->where('sku', $tmpSku)->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }


    /**
     * Test edit text field: đưa text vào, save , refress, check
     */
    public function t1SkuProductEmptySku() {
//        global $tmpProName, $tmpSku, $tmpSkuVal;
        $tmpProName =  "test_new_product_demo_glx";
        $tmpSku = "test_new_sku_demo_glx";
        $tmpSkuVal = "sku.val.glx";
//        Product::withTrashed()->where('name', 'LIKE' , 'test_new_product_demo_glx.%')->forceDelete();
//        Sku::withTrashed()->where('name', 'LIKE' , "$tmpSku%")->forceDelete();
//        Sku::withTrashed()->where('name', $tmpSku)->forceDelete();

        dump(' test '.__FUNCTION__);
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $browser->visit("/admin/product/create");
//        dump(" ID =$obj->id ");

        $nameTmp = "$tmpProName.".microtime(1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='name']")->sendKeys($nameTmp);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $browser->assertSee($nameTmp);

        $idNew = basename($drv->getCurrentURL());
        //lay ra ID:
//        $idNew = clsTestBase2::findOneByXPath('//.divTable2Row[@data-field="id"]')->getAttribute('data-id');

        echo "\n ID New: $idNew";

        //SKU sẽ không có ID prod mới khi chưa save SKU
        $this->assertTrue(Sku::where("product_id", $idNew)->first() == null);


        //        $input = clsTestBase2::findOneByXPath("//input[@data-cmd='add_new_gr_sku']")->sendKeys($tmpSku.'1');
        //Sau khi save SKU sẽ có ID produc mới
        $drv->findElement(WebDriverBy::id('saveProductOption1'))->click();
        sleep(2);
        $skuNew = (Sku::where("product_id", $idNew)->first());
        $this->assertTrue($skuNew != null);


        $skuNew->price = PRICE_SAMPLE_GLX;
        $skuNew->save();


        $browser->refresh();
        sss(1);
        //và ở produc, sku sẽ có id mới
        $tblText = clsTestBase2::findOneByClassName("get_all_sku")->getText();
        $this->assertTrue(strstr($tblText, $skuNew->id) != false);
        $this->assertTrue(strstr($tblText, "SKUId") != false);

        return $idNew;
    }

    public function t1SkuProductNewSku($del = 1) {

//        global $tmpProName, $tmpSku, $tmpSkuVal;
        $tmpProName =  "test_new_product_demo_glx";
        $tmpSku = "test_new_sku_demo_glx";
        $tmpSkuVal = "sku.val.glx";

        dump(' test '.__FUNCTION__);
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $browser->visit("/admin/product/create");
//        dump(" ID =$obj->id ");

        $nameTmp = "$tmpProName.".microtime(1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='name']")->sendKeys($nameTmp);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $browser->assertSee($nameTmp);

        $proid = $idNew = basename($drv->getCurrentURL());



        //Thêm 1 sku và các varial
        clsTestBase2::findOneByXPath("//input[@data-cmd='add_new_gr_sku']")->sendKeys($tmpSku);
        clsTestBase2::findOneByXPath("//button[@data-cmd='add_new_gr_sku']")->click();
//        sss(111);
        usleep(100000);
        clsTestBase2::findOneByXPath("//input[@data-cmd='add_new_var']")->sendKeys("$tmpSkuVal.1");
        clsTestBase2::findOneByXPath("//button[@data-cmd='add_new_var']")->click();
        usleep(100000);
        clsTestBase2::findOneByXPath("//input[@data-cmd='add_new_var']")->sendKeys("$tmpSkuVal.2");
        clsTestBase2::findOneByXPath("//button[@data-cmd='add_new_var']")->click();

        sss(1);
        echo "\n ID New: $idNew";
        //SKU sẽ không có ID prod mới khi chưa save SKU
        $this->assertTrue(Sku::where("product_id", $idNew)->first() == null);
        //        $input = clsTestBase2::findOneByXPath("//input[@data-cmd='add_new_gr_sku']")->sendKeys($tmpSku.'1');
        //Sau khi save SKU sẽ có ID produc mới
        $drv->findElement(WebDriverBy::id('saveProductOption1'))->click();
        sleep(2);
        $skuNew = (Sku::where("product_id", $idNew)->first());
        $this->assertTrue($skuNew != null);

        $skuNew->price = PRICE_SAMPLE_GLX;
        $skuNew->save();

        $browser->refresh();
        sss(1);
        //và ở produc, sku sẽ có id mới và các name mới
        $tblText = clsTestBase2::findOneByClassName("get_all_sku")->getText();
        $this->assertTrue(strstr($tblText, $skuNew->id) != false);
        $this->assertTrue(strstr($tblText, "SKUId") != false);
        $this->assertTrue(strstr($tblText, "$tmpSku") != false);

        if($del)
            $this->delDataTmp();

        return $proid;
    }

    function testProductOrder1()
    {
        $this->delDataTmp();
        //Tạo 2 prod mới với 2 sku
        $proId1 = $this->t1SkuProductEmptySku();
        $proId2 = $this->t1SkuProductEmptySku();

        echo "\n ProId = $proId1 / $proId2 ";

        //Tạo oder mới
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $browser->visit("/admin/order-info/create");

        $uid = Auth::id();
        clsTestBase2::findOneByXPath("//input[@data-field='user_id']")->sendKeys($uid);

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        //data-field

        sss(1);

        clsTestBase2::findOneContainClass("button", "btn_select_service")->click();
        sss(1);

        //Lay 2 san pham dau
        $cc = 0;
        foreach (clsTestBase2::findAllContainClass("input", "check_sku_one_to_buy") AS $oneInput){
            $cc++;
            $oneInput->click();
            if($cc >= 2)
                break;
        }


        //Ở trên sp đã đặt giá 1000
        clsTestBase2::findOneContainClass("input", "quantity_input")->clear()->sendKeys(2);

        clsTestBase2::findOneById('confirm_product_list')->click();

        $browser->refresh();
        sss(2);
        $priceGet = clsTestBase2::findOneById("total_price_order")->getText();
        dump("Price = $priceGet");

//        sss(111);

        $this->assertTrue(intval($priceGet) > PRICE_SAMPLE_GLX);



        //Vào member xem:
        $browser->visit("/member/order-info");
        $priceGet = clsTestBase2::findOneById("total_price_order")->getText();
        dump("Price = $priceGet");

        $this->assertTrue(intval($priceGet) > PRICE_SAMPLE_GLX );

        //Xoa het của user này, trong 1 phút gần nhất
        OrderInfo::where('user_id', $uid)->where("created_at" ,'>', nowyh(time() - 60 ))->forceDelete();

    }
}
