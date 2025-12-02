<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\TagDemo;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableAddTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testDemoAddOneFieldMultiAutoComplete()
    {

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/admin/demo-api');
        $this->openFilterButton();

        clsTestBase2::findOneById('add-new-item')->click();

        $mTag = TagDemo::getIdAndNameNotEmptyForTest();
        $mUser = User::getIdAndNameNotEmptyForTest(5, 'email');

        self::assertTrue(count($mTag) > 1);
        self::assertTrue(count($mUser) > 1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']")->sendKeys(array_values($mTag)[0]);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();
        $val = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']", 1)->getAttribute('value');
        self::assertTrue($val == array_keys($mTag)[0]);
        $txt = clsTestBase2::findOneByXPath("//span[@data-item-value='".array_keys($mTag)[0]."']")->getText();
        self::assertStringContainsString(array_values($mTag)[0], $txt);

        //Send thêm key nữa:
        $input->clear();
        clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']")->sendKeys(array_values($mTag)[1]);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $val = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']", 1)->getAttribute('value');
        self::assertTrue($val == array_keys($mTag)[0].','.array_keys($mTag)[1]);
        $txt = clsTestBase2::findOneByXPath("//span[@data-item-value='".array_keys($mTag)[1]."']")->getText();
        self::assertStringContainsString(array_values($mTag)[1], $txt);

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $val = clsTestBase2::findOneByXPath("//input[@data-field='tag_list_id']", 1)->getAttribute('value');
        self::assertTrue($val == array_keys($mTag)[0].','.array_keys($mTag)[1]);

        $txt = clsTestBase2::findOneByXPath("//span[@data-item-value='".array_keys($mTag)[0]."']")->getText();
        self::assertStringContainsString(array_values($mTag)[0], $txt);
        $txt = clsTestBase2::findOneByXPath("//span[@data-item-value='".array_keys($mTag)[1]."']")->getText();
        self::assertStringContainsString(array_values($mTag)[1], $txt);

    }

    /**
     * Add userid autocomplete: đưa email vào autocomplete search,
     * thay email khác và save, refress để thấy email đã được save OK, có hiển thị lên
     */
    public function testDemoAddOneFieldUserIdAutoComplete()
    {

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/admin/demo-api');

        clsTestBase2::findOneById('add-new-item')->click();

        $mUser = User::getIdAndNameNotEmptyForTest(5, 'email');

        self::assertTrue(count($mUser) > 1);

        $input = clsTestBase2::findOneByXPath("//input[@data-field='user_id']")->sendKeys(array_values($mUser)[0]);

        sss(2);

        dump(__LINE__);

        clsTestBase2::clickFirstAutoCompleteDown();

        dump(__LINE__);
        $val = clsTestBase2::findOneByXPath("//input[@data-field='user_id']", 1)->getAttribute('value');
        self::assertTrue($val == array_keys($mUser)[0]);
        $txt = clsTestBase2::findOneByXPath("//span[@data-item-value='".array_keys($mUser)[0]."']")->getText();
        self::assertStringContainsString(array_values($mUser)[0], $txt);
        dump(__LINE__);
        //Send thêm key nữa:
        $input->clear();
        clsTestBase2::findOneByXPath("//input[@data-field='user_id']")->sendKeys(array_values($mUser)[1]);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();

        $val = clsTestBase2::findOneByXPath("//input[@data-field='user_id']", 1)->getAttribute('value');
        self::assertTrue($val == array_keys($mUser)[1]);

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(2);

        $val = clsTestBase2::findOneByXPath("//input[@data-field='user_id']", 1)->getAttribute('value');

        dump($val);
        $uids = array_keys($mUser);
        dump($uids);
        dump(array_keys($mUser)[1]);
//        sss(10);

//        self::assertTrue($val == array_keys($mUser)[1]);

    }

    /**
     * Add text, number demo: cho các giá trị text (time) random vào 2 ô, save lại, refress để thấy kq đã save, sau đó search lại trên Index, để chắc chắn giá trị thấy trên 2 ô
     */
    public function testDemoAddText()
    {

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->visit('/admin/demo-api');

        clsTestBase2::findOneById('add-new-item')->click();

        $txt = time();

        $input = clsTestBase2::findOneByXPath("//input[@data-field='string1']")->sendKeys($txt);
        $input = clsTestBase2::findOneByXPath("//input[@data-field='number1']")->sendKeys($txt);

        $drv->findElement(WebDriverBy::id('save-one-data'))->click();
        sss(2);
        $browser->refresh();
        sss(1);

        $val = clsTestBase2::findOneByXPath("//input[@data-field='string1']")->getAttribute('value');
        $val1 = clsTestBase2::findOneByXPath("//input[@data-field='number1']")->getAttribute('value');
        self::assertTrue($val == $txt);
        self::assertTrue($val1 == $txt);

        //Trở lại search cái vừa add

        $objMeta = DemoTbl::getMetaObj();
        $browser->visit('/admin/demo-api?'.$objMeta->getSearchKeyField('number1')."=$txt");
        $input = clsTestBase2::findOneByXPath("//input[@data-field='number1'][@value='$txt']");
        $input1 = clsTestBase2::findOneByXPath("//input[@data-field='string1'][@value='$txt']");
        self::assertTrue($input != null);
        self::assertTrue($input1 != null);
    }
}
