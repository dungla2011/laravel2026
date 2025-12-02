<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\Tag;
use App\Models\TagDemo;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableEditTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    /**
     * Test multi field autocomplete: đưa tag name vào , thay đổi, save, kiểm tra KQ
     */
    public function testEditOneDemoMultiAutoComplete()
    {
        dump(' test '.__FUNCTION__);

        $obj = DemoTbl::first();

        $mTag = TagDemo::where('name', '>', '0')->distinct('name')->get();
        $tag = $mTag[0];

        self::assertTrue(count($mTag) > 2, ' Chắc chắn có 3 tag, để chuẩn bị auto complete');

        $this->assertNotNull($tag);
        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();

        $browser->visit("/admin/demo-api/edit/$obj->id");

        //Xóa hết click auto nếu có
        for ($i = 1; $i < 20; $i++) {
            if ($found = clsTestBase2::findOneByXPath("//span[contains(@class, 'span_auto_complete')]")) {
                $found->click();
            }
        }

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']");
        $one->sendKeys($tag->name);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        sss(1);
        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id'][contains(@class, 'input_value_to_post')]");
        //        $this->assertNotNull($one);

        //        dump($one);

        //Todo xxx ko hiểu sao ko  nhận được value ở đây
        dump('CP1: '.$one->getAttribute('value')." / $tag->id / ".$one->getDomProperty('value'));
        sss(1);

        $this->assertTrue($one->getAttribute('value') == $tag->id, $one->getAttribute('value')." / $tag->id");

        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'tag_list_id')][contains(text(), '$tag->name')]");
        $this->assertNotNull($found);

        //Save lại và check lại
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $tag->id);
        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'tag_list_id')][contains(text(), '$tag->name')]");
        $this->assertNotNull($found);

        //Đưa theem 2 tag vào:
        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']");
        $one->sendKeys($mTag[1]->name);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $one->clear();

        $one->sendKeys($mTag[2]->name);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $mTag[0]->id.','.$mTag[1]->id.','.$mTag[2]->id);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $one = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id'][contains(@class, 'input_value_to_post')]");

        $this->assertNotNull($one);

        dump('VAL = '.$mTag[0]->id.','.$mTag[1]->id.','.$mTag[2]->id);
        dump('VS : ');
        dump($one->getAttribute('value'));

        $this->assertTrue($one->getAttribute('value') == $mTag[0]->id.','.$mTag[1]->id.','.$mTag[2]->id);

        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'tag_list_id')][contains(text(), '".$mTag[0]->name."')]");
        $this->assertNotNull($found);
        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'tag_list_id')][contains(text(), '".$mTag[1]->name."')]");
        $this->assertNotNull($found);
        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'tag_list_id')][contains(text(), '".$mTag[2]->name."')]");
        $this->assertNotNull($found);
        //
        //
        //
        //        sss(10);

    }

    /**
     * Test Edit email autocomplete: đưa email vào để autocomplete , save, kiểm tra kq
     */
    public function testEditOneDemoSingleAutoComplete()
    {

        dump(' test '.__FUNCTION__);

        $obj = DemoTbl::first();

        $mUser = User::where('email', '>', '0')->distinct('email')->get();

        self::assertTrue(count($mUser) > 1, ' Chắc chắn có 2 user, để chuẩn bị auto complete');

        $user = $mUser[0];

        $this->assertNotNull($user);
        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();

        $browser->visit("/admin/demo-api/edit/$obj->id");

        //Xóa hết click auto nếu có
        for ($i = 1; $i < 20; $i++) {
            if ($found = clsTestBase2::findOneByXPath("//span[contains(@class, 'span_auto_complete')]")) {
                $found->click();
            }
        }

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id']");
        $one->sendKeys($user->email);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $user->id);

        //Save lại và check lại
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $user->id);

        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'user_id')][contains(text(), '".$mUser[0]->email."')]");
        $this->assertNotNull($found);

        //Đưa user khacs vào:
        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id']");
        $one->sendKeys($mUser[1]->email);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $mUser[1]->id);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();

        //Save sau do refresh kiem tra lai
        sss(2);
        $browser->refresh();
        sss(1);
        $one = clsTestBase2::findOneByXPath("//input[@data-field='user_id'][contains(@class, 'input_value_to_post')]");
        $this->assertNotNull($one);
        $this->assertTrue($one->getAttribute('value') == $mUser[1]->id);

        $found = clsTestBase2::findOneByXPath("//span[contains(@data-autocomplete-id, 'user_id')][contains(text(), '".$mUser[1]->email."')]");
        $this->assertNotNull($found);

        //
        //
        //
        //        sss(10);

    }

    /**
     * Test demo status: bật tắt status, save, kiểm tra kq
     */
    public function testCheckDemoEditStatus()
    {
        dump(' test '.__FUNCTION__);

        $obj = DemoTbl::first();
        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit("/admin/demo-api/edit/$obj->id");

        $item = clsTestBase2::findOneByXPath("//i[contains(@class,'change_status_item')][@data-field='status']");

        $cls = $item->getAttribute('class');

        if (strstr($cls, 'fa-toggle-off') !== false) {
            self::assertTrue(! $obj->status);
            $input = clsTestBase2::findOneByXPath("//input[@data-field='status']");
            self::assertTrue(! $input->getAttribute('value'));
            $item->click();
            self::assertTrue($input->getAttribute('value') == 1);

            //Save lại và check lại
            $drv->findElement(WebDriverBy::id('save-one-data'))->click();
            sss(2);
            $browser->refresh();
            sss(1);
            $input = clsTestBase2::findOneByXPath("//input[@data-field='status']");
            self::assertTrue($input->getAttribute('value') == 1);

        } elseif (strstr($cls, 'fa-toggle-on') !== false) {
            self::assertTrue($obj->status == 1);

            $input = clsTestBase2::findOneByXPath("//input[@data-field='status']");
            self::assertTrue($input->getAttribute('value') == 1);
            $item->click();
            self::assertTrue(! $input->getAttribute('value'));

            //Save lại và check lại
            $drv->findElement(WebDriverBy::id('save-one-data'))->click();
            sss(2);
            $browser->refresh();
            sss(1);
            $input = clsTestBase2::findOneByXPath("//input[@data-field='status']");
            self::assertTrue(! $input->getAttribute('value'));

        } else {
            self::assertTrue(false, ' Khong thay On/Off');
        }
    }

    /**
     * Test Select Edit Option Demo: click vào các giá trị select và save... kiểm tra KQ
     */
    public function testDemoEditSelectDropDown()
    {
        dump(' test '.__FUNCTION__);

        $obj = DemoTbl::first();
        //        $obj = DemoTbl::where('id',154)->first();
        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit("/admin/demo-api/edit/$obj->id");

        dump(" ID =$obj->id ");

        $select = clsTestBase2::findOneByXPath("//select[@data-field='string2']");

        $val = $select->getAttribute('value');

        dump("VAL = $val / $obj->string2");
        dump($obj->status);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string2']");

        dump('VAL Input = '.$input->getAttribute('value'));
        self::assertTrue($obj->string2 == $input->getAttribute('value'));

        $objMeta = DemoTbl::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();
        $objMeta = $mMeta['string2'];
        $retArray = $objMeta->callJoinFunction($objMeta->is_select);

        dump(" JoinFunc: $objMeta->is_select ");

        $aKey = array_keys($retArray);

        dump($aKey);
        $changeOption = 0;
        if ($val != $aKey[1]) {
            $changeOption = $aKey[1];
        } else {
            $changeOption = $aKey[2];
        }

        $select->findElement(WebDriverBy::cssSelector("option[value='$changeOption']"))->click();

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string2']");

        dump(' VAL New = '.$input->getAttribute('value').' / Change To = '.$changeOption);
        //
        //        sss(100);

        self::assertTrue($input->getAttribute('value') == $changeOption);

        //Save lại và check lại
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string2']");
        self::assertTrue($input->getAttribute('value') == $changeOption);
    }

    /**
     * Test edit text field: đưa text vào, save , refress, check
     */
    public function testDemoEditTextField()
    {
        dump(' test '.__FUNCTION__);

        $obj = DemoTbl::first();
        //        $obj = DemoTbl::where('id',154)->first();
        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit("/admin/demo-api/edit/$obj->id");

        dump(" ID =$obj->id ");

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string1']");
        $val0 = $input->getAttribute('value');

        $val1 = 'test'.time();

        $input->clear();
        $input->sendKeys($val1);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string1']");
        self::assertTrue($input->getAttribute('value') == $val1);

        //Set Empty
        $input->clear();
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        //Trowr laij value ban dau
        $input = clsTestBase2::findOneByXPath("//input[@data-field='string1']");
        self::assertTrue($input->getAttribute('value') == '');
        $input->sendKeys($val0);
        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);
        $input = clsTestBase2::findOneByXPath("//input[@data-field='string1']");
        self::assertTrue($input->getAttribute('value') == $val0);

    }
}
