<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\Tag;
use App\Models\TagDemo;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testDemoAccessTable()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $browser->visit('/admin/demo-api');
        //        $browser->assertSee("Đà nẵng");
        //        $browser->assertSee("abc.com");
        //
        //        $browser->assertSeeIn('textarea1[]', '66');

        $sl = 'input[data-autocomplete-id="47-textarea1"]';
        //First input has class textarea1
        $sl = '.textarea1';
        $sl = "(//input[@data-field='textarea1'])[1]";
        //OK1
        //$browser->assertAttributeContains('input[id=x222]','value', 111);
        //OK2
        //        $browser->assertAttributeContains($sl,'value', 66);
        //OK3
        //$browser->type('input[id=x222]','xxxx');

        $randomVal = time();
        //Nhập vào 1 số và enter
//        $browser->type($sl, $randomVal);
//        $browser->keys($sl, '{enter}');

        $sl2 = "(//input[@data-field='textarea1'])[1]";
        $drv->findElement(WebDriverBy::xpath($sl2))->clear()
            ->sendKeys($randomVal)->sendKeys(WebDriverKeys::RETURN_KEY);

        sss(1);
        //$sl2 = '.textarea1:nth-child(2)';
        //OK Phần tử thứ nth:
        $sl2 = "(//input[@data-field='textarea1'])[3]";
        $drv->findElement(WebDriverBy::xpath($sl2))->clear()
            ->sendKeys($randomVal + 1)->sendKeys(WebDriverKeys::RETURN_KEY);

        sss(2);

        $browser->refresh();

        sss(2);

        $valAfterRefresh = $drv->findElement(WebDriverBy::xpath($sl2))->getAttribute('value');

        //Sau khi refresh, xem số đó đã được ghi vào db chưa:
//        $browser->assertAttributeContains($sl, 'value', $randomVal);
        $this->assertTrue($drv->findElement(WebDriverBy::xpath($sl))->getAttribute('value') == $randomVal);
        $this->assertTrue($valAfterRefresh == $randomVal + 1);

    }

    //Kiểm tra save all trên các input text
    public function testSaveAllDemoTable()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }

        $this->testLoginTrueAcc();

        $browser = $this->getBrowserLogined();

        $drv = $this->getDrv();
        $randomVal = time();
        clsTestBase2::$driver = $drv;

        $browser = $this->getBrowserLogined();
        $url = $browser->driver->getCurrentURL();

        $browser->visit('/admin/demo-api');
        ////////////////////////////////////////////////////
        //save-all-data
        //OK Phần tử thứ nth:
        $sl3 = "(//input[@data-field='textarea1'])[1]";
        $val3Old = clsTestBase2::findOneByXPath($sl3)->getAttribute('value');
        $drv->findElement(WebDriverBy::xpath($sl3))->clear()
            ->sendKeys($randomVal + 2)->sendKeys(WebDriverKeys::RETURN_KEY);

        $sl4 = "(//input[@data-field='textarea1'])[2]";
        $val4Old = clsTestBase2::findOneByXPath($sl4)->getAttribute('value');
        $drv->findElement(WebDriverBy::xpath($sl4))->clear()
            ->sendKeys($randomVal + 2)->sendKeys(WebDriverKeys::RETURN_KEY);

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);

        $drv->navigate()->refresh();
        sss(2);

        //Sau khi refresh, xem số đó đã được ghi vào db chưa:
        $valAfterRefresh = $drv->findElement(WebDriverBy::xpath($sl3))->getAttribute('value');
        $this->assertTrue($valAfterRefresh == $randomVal + 2, " $valAfterRefresh == $randomVal + 2 ");

        $valAfterRefresh = $drv->findElement(WebDriverBy::xpath($sl4))->getAttribute('value');
        $this->assertTrue($valAfterRefresh == $randomVal + 2);

        //Trở lại val cũ:
        clsTestBase2::findOneByXPath($sl3)->clear()->sendKeys($val3Old)->sendKeys(WebDriverKeys::ENTER);
        clsTestBase2::findOneByXPath($sl4)->clear()->sendKeys($val4Old)->sendKeys(WebDriverKeys::ENTER);

    }

    //Kiểm tra check trạng thái status, bật tắt
    public function testCheckStatusDemo()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/demo-api');

        clsTestBase2::$driver = $drv;

        sss(2);
        $slInput = "//input[@data-field='status']";
        $valPre = clsTestBase2::findOneByXPath($slInput)->getAttribute('value');
        $slStatus = "//i[contains(@class,'change_status_item')][@data-field='status']";
        clsTestBase2::findOneByXPath($slStatus)->click();

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        //      if($cls = clsTestBase2::findOneByXPath($sl1)->getAttribute('class'))
        //           dump(" CLS = $cls ");
        sss(2);
        $browser->refresh();
        sss(2);
        $valAfter = clsTestBase2::findOneByXPath($slInput)->getAttribute('value');
        dump("Check : $valPre != $valAfter");
        $this->assertTrue($valPre != $valAfter);

        //Trở lại giá trị ban đầu
        clsTestBase2::findOneByXPath($slStatus)->click();

        //data-name="id[readOnly]"
    }

    /**
     * Check Save all không làm thay đổi giá trị status
     */
    public function testCheckMultiStatusDemo()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/demo-api');

        clsTestBase2::$driver = $drv;

        sss(2);
        $slInput1 = "(//input[@data-field='status'])[1]";
        $valPre1 = clsTestBase2::findOneByXPath($slInput1)->getAttribute('value');
        $slStatus1 = "(//i[contains(@class,'change_status_item')][@data-field='status'])[1]";
        clsTestBase2::findOneByXPath($slStatus1)->click();

        $slInput2 = "(//input[@data-field='status'])[2]";
        $valPre2 = clsTestBase2::findOneByXPath($slInput2)->getAttribute('value');
        $slStatus2 = "(//i[contains(@class,'change_status_item')][@data-field='status'])[2]";
        clsTestBase2::findOneByXPath($slStatus2)->click();

        //      if($cls = clsTestBase2::findOneByXPath($sl1)->getAttribute('class'))
        //           dump(" CLS = $cls ");

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $valAfter1 = clsTestBase2::findOneByXPath($slInput1)->getAttribute('value');
        dump("Check : $valPre1 != $valAfter1");
        $this->assertTrue($valPre1 != $valAfter1);
        $valAfter2 = clsTestBase2::findOneByXPath($slInput2)->getAttribute('value');
        dump("Check : $valPre2 != $valAfter2");
        $this->assertTrue($valPre2 != $valAfter2);

        //Trở lại ban đầu
        clsTestBase2::findOneByXPath($slStatus1)->click();
        clsTestBase2::findOneByXPath($slStatus2)->click();

        //data-name="id[readOnly]"
    }

    //Kiểm tra thay đổi giá trị trên select option
    public function testSelectOptionDemo()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/demo-api');

        clsTestBase2::$driver = $drv;

        sss(2);

        $slInput1 = "//select[contains(@class,'sl_option')]";
        $valPre1 = clsTestBase2::findOneByXPath($slInput1, 1)->getAttribute('value');
        $valSet1 = $valPre1 == 1 ? 2 : 1;
        clsTestBase2::findOneByXPath($slInput1, 1)->findElement(WebDriverBy::cssSelector("option[value='$valSet1']"))->click();

        $valPre2 = clsTestBase2::findOneByXPath($slInput1, 4)->getAttribute('value');
        $valSet2 = $valPre2 == 1 ? 2 : 1;
        clsTestBase2::findOneByXPath($slInput1, 4)->findElement(WebDriverBy::cssSelector("option[value='$valSet2']"))->click();

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();

        sss(2);
        $browser->refresh();

        $valAfter1 = clsTestBase2::findOneByXPath($slInput1, 1)->getAttribute('value');
        $valAfter2 = clsTestBase2::findOneByXPath($slInput1, 4)->getAttribute('value');
        $this->assertTrue($valAfter1 == $valSet1);
        $this->assertTrue($valAfter2 == $valSet2);

        //Đổi về giá trị ban đầu, rồi SAVE ALL
        //và refresh, sau đó check xem có đúng giá trị ban đầu không:
        clsTestBase2::findOneByXPath($slInput1, 1)->findElement(WebDriverBy::cssSelector("option[value='$valPre1']"))->click();
        clsTestBase2::findOneByXPath($slInput1, 4)->findElement(WebDriverBy::cssSelector("option[value='$valPre2']"))->click();
        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();
        $valAfter1 = clsTestBase2::findOneByXPath($slInput1, 1)->getAttribute('value');
        $valAfter2 = clsTestBase2::findOneByXPath($slInput1, 4)->getAttribute('value');
        $this->assertTrue($valAfter1 == $valPre1);
        $this->assertTrue($valAfter2 == $valPre2);

        dump(" VAL1 = $valPre1 ");
        dump(" VAL2 = $valPre2 ");
        //sl_option
    }

    public function getXPathAutoCompleteDropDown()
    {
        return "//ul[contains(@class,'ui-autocomplete')][not(contains(@style,'display: none'))]//div[@class='ui-menu-item-wrapper']";
    }

    //Kiểm tra tìm autocomplete trên userid, find vào 02 input đầu tiên
    public function testCheckAutoCompleteUid()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        //data-api-search-field="email"

        $mm = User::all()->toArray();
        if (count($mm) < 2) {
            exit("\n\n Không đủ số email test!");
        }

        $idWillSet0 = $id0 = $mm[0]['id'];
        $email0 = $mm[0]['email'];
        $idWillSet1 = $id1 = $mm[1]['id'];
        $email1 = $mm[1]['email'];

        if (! strstr($email0, '@') || ! strstr($email1, '@')) {
            exit("\n\n Not valid email: $email0 / $email1");
        }

        //Lấy các giá trị UID đang được SET:
        $slInputHide = '//input[@data-field="user_id"]';
        //Tìm 2 input đầu tiên
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');

        //Đổi chỗ email, để khi SET có khác biệt
        if ($uidDB0 == $id0) {
            $email0 = $mm[1]['email'];
            $idWillSet0 = $id1;
        }
        if ($uidDB1 == $id1) {
            $email1 = $mm[0]['email'];
            $idWillSet1 = $id0;
        }

        $slInput1 = '//input[@data-api-search-field="email"][not(contains(@class,"search_top_grid"))]';
        //text sổ xuống AutoComplete sẽ nằm trong ul:

        clsTestBase2::findOneByXPath($slInput1, 0)->sendKeys(substr($email0, 0, -1));
        sss(2);
        //        sss(200);
        //Click vào email sổ xuống
        //        $slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //        clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();

        clsTestBase2::findOneByXPath($slInput1, 1)->sendKeys(substr($email1, 0, -1));
        sss(2);
        //Click vào email sổ xuống
        //clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();

        sss(1);
        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();

        $dtId1 = clsTestBase2::findOneByXPath($slInput1, 0)->getAttribute('data-autocomplete-id');
        $dtId2 = clsTestBase2::findOneByXPath($slInput1, 1)->getAttribute('data-autocomplete-id');
        dump("dtId1/2  = $dtId1 / $dtId2");
        $slInputHiden1 = '//input[@data-field="user_id"][@data-autocomplete-id="'.$dtId1.'"]';
        $slInputHiden2 = '//input[@data-field="user_id"][@data-autocomplete-id="'.$dtId2.'"]';

        $idSetDone0 = clsTestBase2::findOneByXPath($slInputHiden1, 0)->getAttribute('value');
        $idSetDone1 = clsTestBase2::findOneByXPath($slInputHiden2, 0)->getAttribute('value');

        dump("VAL ID = $idSetDone0 / $idSetDone1");

        $this->assertTrue($idSetDone0 == $idWillSet0);
        $this->assertTrue($idSetDone1 == $idWillSet1);

        //        sss(5);

    }

    //Kiểm tra click remove single autocomplete, xem có remove về empty được không
    public function testAddRemoveSingleUserId()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //Gọi hàm này để chắc chắn sẽ find 2 uid vào 2 ô grid đầu tiên:
        $this->testCheckAutoCompleteUid();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;
        //data-api-search-field="email"

        //Lấy các giá trị UID đang được SET:
        $slInputHide = '//input[@data-field="user_id"]';
        //Tìm 2 input đầu tiên
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');

        //Lấy ra ID Auto Comp:
        $idDataAutoCom0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('data-autocomplete-id');
        $idDataAutoCom1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('data-autocomplete-id');

        //Nếu là Empty, thì fill vào:

        dump("UID0 = $uidDB0, id data0 = $idDataAutoCom0");
        dump("UID1 = $uidDB1, id data1 = $idDataAutoCom1");

        //Remove UID nếu có
        if ($span0 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom0.'"]')) {
            dump(' FOUIND 0, '.$span0->getText());
            $span0->click();
            //Save All
            $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        }

        //Remove UID nếu có
        if ($span1 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom1.'"]')) {
            dump(' FOUIND 1, '.$span1->getText());
            $span1->click();
            //Save All
            $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        }

        sss(2);

        //Sau khi remove, check xem còn tồn tại không, nếu 01 value ko tồn tại thì là đúng:
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');
        dump("UID0 = $uidDB0");
        dump("UID1 = $uidDB1");

        $this->assertIsBool(! ($uidDB0));
        $this->assertIsBool(! ($uidDB1));

        //Không thấy 2 span là ok
        $span1 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom0.'"]');
        $span2 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom1.'"]');
        $this->assertIsBool(! ($span1));
        $this->assertIsBool(! ($span2));

    }

    //Kiểm tra search multivalue của demo
    public function testAddMultiValueTagListDemo()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        $this->testLoginTrueAcc();

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $mTag = TagDemo::all()->toArray();
        //Chỉ lấy các Name > 3 để search
        $mTagOK = [];
        foreach ($mTag as $tag) {
            if (strlen($tag['name']) > 2) {
                $mTagOK[] = $tag;
            }
        }
        $this->assertTrue(count($mTagOK) > 2);

        self::assertTrue(count($mTag) > 2);
        //Remove hết tag nếu có:
        //tìm input data-field = tag_list_id
        //Lấy các giá trị UID đang được SET:
        $slInputHide = "//input[@data-field='tag_list_id'][@data-edit-able='1']";
        $slInputHide = "//input[@data-field='tag_list_id']";
        //Tìm 2 input tag_list_id đầu tiên
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');

        //Lấy ra ID Auto Comp:
        $idDataAutoCom0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('data-autocomplete-id');
        $idDataAutoCom1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('data-autocomplete-id');
        $idDataAutoCom2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('data-autocomplete-id');

        dump("UID0 = $uidDB0, id data0 = $idDataAutoCom0");
        dump("UID1 = $uidDB1, id data1 = $idDataAutoCom1");
        dump("UID1 = $uidDB2, id data1 = $idDataAutoCom2");

        //Remove tất cả span nếu có
        //Remove UID nếu có
        //        for ($i = 0; $i < 10; $i++)//Remove hết, vì có thể >1 phần tử
        //            if ($span0 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="' . $idDataAutoCom0 . '"]')) {
        //                dump(" FOUIND 0, " . $span0->getText());
        //                $span0->click();
        //            }
        //        for ($i = 0; $i < 10; $i++)//Remove hết, vì có thể >1 phần tử
        //            if ($span1 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="' . $idDataAutoCom1 . '"]')) {
        //                dump(" FOUIND 0, " . $span1->getText());
        //                $span1->click();
        //            }
        //
        //        for ($i = 0; $i < 10; $i++)//Remove hết, vì có thể >1 phần tử
        //            if ($span2 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="' . $idDataAutoCom2 . '"]')) {
        //                dump(" FOUIND 0, " . $span2->getText());
        //                $span2->click();
        //            }

        foreach ($drv->findElements(WebDriverBy::xpath('//span[@data-autocomplete-id="'.$idDataAutoCom0.'"]')) as $elm) {
            $elm->click();
        }
        foreach ($drv->findElements(WebDriverBy::xpath('//span[@data-autocomplete-id="'.$idDataAutoCom1.'"]')) as $elm) {
            $elm->click();
        }
        foreach ($drv->findElements(WebDriverBy::xpath('//span[@data-autocomplete-id="'.$idDataAutoCom2.'"]')) as $elm) {
            $elm->click();
        }

        //Save All
        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();
        sss(2);
        //Sau khi remove, saveAll, kiểm tra lại 2 input ban đầu, phải Empty:
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');

        dump("UID: $uidDB0, $uidDB1, $uidDB2  / ".clsTestBase2::findOneByXPath($slInputHide, 1)->getDomProperty('value'));
        sss(1);
        $this->assertTrue(! ($uidDB0));
        $this->assertTrue(! ($uidDB1));
        $this->assertTrue(! ($uidDB2));

        //Bắt đầu thêm vào:
        //Tìm data-autocomplete-id=*-tag_list_id
        $inputSearch = "//input[contains(@data-autocomplete-id,'-tag_list_id')][@data-api-search-field='name']";
        clsTestBase2::findOneByXPath($inputSearch, 0)->sendKeys($mTagOK[0]['name']);
        sss(2);
        //Click vào auto sổ xuống
        //$slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();

        //Tìm data-autocomplete-id=*-tag_list_id
        clsTestBase2::findOneByXPath($inputSearch, 1)->clear();
        clsTestBase2::findOneByXPath($inputSearch, 1)->sendKeys($mTagOK[1]['name']);
        sss(2);
        //Click vào auto sổ xuống
        //$slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();
        clsTestBase2::findOneByXPath($inputSearch, 2)->clear();
        clsTestBase2::findOneByXPath($inputSearch, 2)->sendKeys($mTagOK[0]['name']);
        sss(2);
        //Click vào auto sổ xuống
        //$slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //        clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();
        clsTestBase2::findOneByXPath($inputSearch, 2)->clear();
        clsTestBase2::findOneByXPath($inputSearch, 2)->sendKeys($mTagOK[1]['name']);
        sss(2);

        //Click vào auto sổ xuống
        //$slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //        clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();
        clsTestBase2::findOneByXPath($inputSearch, 2)->clear();
        clsTestBase2::findOneByXPath($inputSearch, 2)->sendKeys($mTagOK[2]['name']);
        sss(2);
        //Click vào auto sổ xuống
        //$slAutoComplete1 = $this->getXPathAutoCompleteDropDown();
        //        clsTestBase2::findOneByXPath($slAutoComplete1, 0)->click();
        clsTestBase2::clickFirstAutoCompleteDown();

        sss(1);

        //Kiểm tra 2 input có id tương ứng:
        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');

        dump(" uidDB = $uidDB2 / $uidDB0 / $uidDB1 / ");

        sss(1);

        //Todo không hiểu sao lấy value ko được
        $this->assertTrue($uidDB0 == $mTagOK[0]['id']);
        $this->assertTrue($uidDB1 == $mTagOK[1]['id']);
        $this->assertTrue($uidDB2 == $mTagOK[0]['id'].','.$mTagOK[1]['id'].','.$mTagOK[2]['id']);

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();

        sss(2);
        //Refresh, vẫn giữ nguyên 2 ID đó
        $browser->refresh();
        sss(2);

        $uidDB0 = clsTestBase2::findOneByXPath($slInputHide, 0)->getAttribute('value');
        $uidDB1 = clsTestBase2::findOneByXPath($slInputHide, 1)->getAttribute('value');
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');
        $this->assertTrue($uidDB0 == $mTagOK[0]['id']);
        $this->assertTrue($uidDB1 == $mTagOK[1]['id']);
        $this->assertTrue($uidDB2 == $mTagOK[0]['id'].','.$mTagOK[1]['id'].','.$mTagOK[2]['id']);

        //Remove bớt đi 1 phần tử ở item3
        if ($span2 = clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom2.'"]')) {
            dump(' FOUIND 0, '.$span2->getText());
            $span2->click();
        }
        sss(1);
        //Kiểm tra xem bị remove chưa
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');
        $this->assertTrue($uidDB2 == $mTagOK[1]['id'].','.$mTagOK[2]['id'], " $uidDB2 / ".$mTagOK[1]['id'].','.$mTagOK[2]['id']);
        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();
        sss(2);
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');
        $this->assertTrue($uidDB2 == $mTagOK[1]['id'].','.$mTagOK[2]['id']);

        //Remove hết nốt 2 phần tử còn lại, xem có empty ko:
        clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom2.'"]')->click();
        clsTestBase2::findOneByXPath('//span[@data-autocomplete-id="'.$idDataAutoCom2.'"]')->click();
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');
        $this->assertTrue(! $uidDB2);
        //Save all and refresh
        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);
        $browser->refresh();
        sss(2);
        $uidDB2 = clsTestBase2::findOneByXPath($slInputHide, 2)->getAttribute('value');
        $this->assertTrue(! $uidDB2);

    }

    /**
     * Tất cả các divTable2Cell đều phải có input_value_to_post, cả cả là disable, readonly
     * Để sẵn sàng post lên Update (sẽ loại trừ các trường update nếu ko có quyền edit able)
     */
    public function testAllTableCellNeedHaveInputValueToPostAndDataIsLoadOKFromDB()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');

        $cc = 0;
        $mm = $drv->findElements(WebDriverBy::xpath("//div[contains(@class, 'divCellDataForTest')]"));
        dump("Count12 = " . count($mm));
        foreach ($mm as $elm) {
            //            dump($cc++);
            $tmp = $elm->findElements(WebDriverBy::xpath("input[contains(@class, 'input_value_to_post')][not(@readonly)]"));
            if(count($tmp) ==0)
                continue;
                       dump("Count = " . count($tmp));
                        dump($tmp[0]->getAttribute('data-id'));
                        dump($tmp[0]->getAttribute('data-field'));
            self::assertTrue(count($tmp) == 1);
            self::assertTrue(! empty($tmp[0]->getAttribute('data-id')));
            self::assertTrue(! empty($tmp[0]->getAttribute('data-field')));

            $field = $tmp[0]->getAttribute('data-field');
            $val = $tmp[0]->getAttribute('value');

            $mObj = DemoTbl::where('id', $tmp[0]->getAttribute('data-id'))->first()->toArray();
            //            dump($val);
            //            dump($mObj);

            if ($field[0] != '_') {
                if (substr($field, -3) != '_at') {
                    if ($mObj[$field] ?? '') {
                        self::assertTrue($mObj[$field] == $val);
                    }
                }
            }
        }

    }
}
