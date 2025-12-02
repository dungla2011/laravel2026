<?php


namespace Tests\Feature;

define("PRICE_SAMPLE_GLX", 1110);

use App\Models\DemoTbl;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\FolderFile;
use App\Models\News;
use App\Models\OrderInfo;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\Sku;
use App\Models\Tag;
use App\Models\TagDemo;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;




/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class NewsTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }



    function delDataTmp()
    {
//        global $tmpProName, $tmpSku, $tmpSkuVal;


    }

    /**
     * Test edit text field: đưa text vào, save , refress, check
     */
    public function testUploadImageTinyMCEAndViewPublic() {
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
        $browser->visit("/admin/news/create");
//        dump(" ID =$obj->id ");

        $nameTmp = "$tmpProName.".microtime(1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='name']")->sendKeys($nameTmp);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(1);
        $browser->refresh();

        sss(1);
        $browser->assertSee($nameTmp);
        $idNew = basename($drv->getCurrentURL());
        //lay ra ID:
//        $idNew = clsTestBase2::findOneByXPath('//.divTable2Row[@data-field="id"]')->getAttribute('data-id');
        echo "\n ID New: $idNew";

        $browser->visit("/admin/news/edit/$idNew");
        sss(1);
        clsTestBase2::findOneById("mceu_17")->click();
        usleep(100000);
        clsTestBase2::findOneById("mceu_50-t1")->click();
        usleep(100000);

        $this->assertTrue(clsTestBase2::findOneById("mceu_58-button") != null);

        $browser->refresh();

        clsTestBase2::findOneById('public_view_item')->click();

        $tmpProName =  "test_new_product_demo_glx";
        News::withTrashed()->where('name', 'LIKE' , $tmpProName.'.%')->forceDelete();

        //Chưa chọn được file
//        clsTestBase2::findOneById("mceu_68-button")->sendKeys('C:\Users\pc2\Pictures\anh3.jpg');
    }
}
