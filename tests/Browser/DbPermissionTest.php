<?php

namespace Tests\Feature;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

class DbPermissionTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testSaveOneCell()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        $this->testLoginTrueAcc(explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS'))[0]);
        $drv = $this->getDrv();

        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/db-permission');
        $browser->assertSee('Edit allow');

        clsTestBase2::$driver = $drv;
        clsTestBase2::findOneById('select_table_model')->findElement(WebDriverBy::cssSelector("option[value='/admin/db-permission?table=demo_tbls']"))->click();

        sss(2);
        //
        //        //  /admin/db-permission?table=demo_sub1s
        ////
        $browser->assertSee('deleted_at');
        $browser->assertSee('tag_list_id');
        //data-field
        //data-meta-field

        $rand = time();

        $sl2 = "//input[@data-field='id'][@data-meta-field='name']";
        //$sl2 = '//*[@id="div_container_meta"]/div/div/div[3]/div[4]/input';
        clsTestBase2::findOneByXPath($sl2)->clear()->sendKeys($rand)->sendKeys(WebDriverKeys::ENTER);
        sss(2);
        $browser->refresh();
        sss(2);
        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        dump("CHECK1 :  $rand == $val ");

        $this->assertTrue($val == $rand);

        //Xóa Item Empty và check
        clsTestBase2::findOneByXPath($sl2)->clear()->sendKeys(WebDriverKeys::ENTER);
        sss(2);
        $browser->refresh();

        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        dump("CHECK2:  '$val' == '' ");
        $this->assertTrue($val == '');

        //Send Userid Name rand
        $sl2 = "//input[@data-field='user_id'][@data-meta-field='name']";
        //$sl2 = '//*[@id="div_container_meta"]/div/div/div[3]/div[4]/input';
        clsTestBase2::findOneByXPath($sl2)->clear()->sendKeys($rand)->sendKeys(WebDriverKeys::ENTER);
        sss(2);
        $browser->refresh();

        //        sss(100);
        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        dump("CHECK3 :  $rand == $val ");

        $this->assertTrue($val == $rand);

        //Xóa Item Empty và check
        clsTestBase2::findOneByXPath($sl2)->clear()->sendKeys(WebDriverKeys::ENTER);
        sss(2);
        $browser->refresh();
        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        dump("CHECK4 :  '$val' == '' ");
        $this->assertTrue($val == '');
    }

    public function testSaveAllCell()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);

        $this->testLoginTrueAcc(explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS'))[0]);
        $drv = $this->getDrv();
        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/db-permission');
        $browser->assertSee('Edit allow');

        clsTestBase2::$driver = $drv;
        clsTestBase2::findOneById('select_table_model')->findElement(WebDriverBy::cssSelector("option[value='/admin/db-permission?table=demo_tbls']"))->click();

        sss(2);
        //
        //        //  /admin/db-permission?table=demo_sub1s
        ////
        $browser->assertSee('deleted_at');
        $browser->assertSee('tag_list_id');
        //data-field
        //data-meta-field

        ////////////////////////////////////////////////////////////////////////
        //Set rand value , save all, then check value
        $rand = time();
        $sl1 = "//input[@data-field='id'][@data-meta-field='name']";
        clsTestBase2::findOneByXPath($sl1)->clear()->sendKeys($rand);
        $sl2 = "//input[@data-field='user_id'][@data-meta-field='name']";
        clsTestBase2::findOneByXPath($sl2)->clear()->sendKeys($rand);
        $drv->findElement(WebDriverBy::id('save_all_form_button'))->click();
        sss(2);

        //Kiểm tra sau khi refresh
        $browser->refresh();
        $val = clsTestBase2::findOneByXPath($sl1)->getAttribute('value');
        dump("CHECK :  $rand == $val ");
        $this->assertTrue($val == $rand);
        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        dump("CHECK :  '$val' == '' ");
        $this->assertTrue($val == $rand);

        ////////////////////////////////////////////////////////////////////////
        //Set empty value , save all, then check empty
        $sl1 = "//input[@data-field='id'][@data-meta-field='name']";
        clsTestBase2::findOneByXPath($sl1)->clear();
        //Send Userid Name rand
        $sl2 = "//input[@data-field='user_id'][@data-meta-field='name']";
        clsTestBase2::findOneByXPath($sl2)->clear();

        $drv->findElement(WebDriverBy::id('save_all_form_button'))->click();
        sss(2);

        //Kiểm tra sau khi refresh, empty value
        $browser->refresh();
        $val = clsTestBase2::findOneByXPath($sl1)->getAttribute('value');
        dump('CHECK :  empty ');
        $this->assertTrue($val == '');
        $val = clsTestBase2::findOneByXPath($sl2)->getAttribute('value');
        $this->assertTrue($val == '');

    }

    public function testCheckStatusDbPer()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $this->testLoginTrueAcc(explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS'))[0]);
        $drv = $this->getDrv();
        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/db-permission');
        $browser->assertSee('Edit allow');

        clsTestBase2::$driver = $drv;
        clsTestBase2::findOneById('select_table_model')->findElement(WebDriverBy::cssSelector("option[value='/admin/db-permission?table=demo_tbls']"))->click();

        return;

        sss(2);
        $sl0 = "//input[@name='id[readOnly]']";
        $valPre = clsTestBase2::findOneByXPath($sl0)->getAttribute('value');
        $sl1 = "//i[@data-name='id[readOnly]']";
        clsTestBase2::findOneByXPath($sl1)->click();
        //      if($cls = clsTestBase2::findOneByXPath($sl1)->getAttribute('class'))
        //           dump(" CLS = $cls ");
        sss(2);
        $browser->refresh();
        $valAfter = clsTestBase2::findOneByXPath($sl0)->getAttribute('value');
        dump("Check : $valPre != $valAfter");
        $this->assertTrue($valPre != $valAfter);

        clsTestBase2::findOneByXPath($sl1)->click();

        //data-name="id[readOnly]"
    }

    public function testManySaveStatusDbPer()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $this->testLoginTrueAcc(explode(',', env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS'))[0]);
        $drv = $this->getDrv();
        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");

        $browser->visit('/admin/db-permission');
        sss(1);
        $browser->assertSee('Edit allow');

        clsTestBase2::$driver = $drv;
        clsTestBase2::findOneById('select_table_model')->findElement(WebDriverBy::cssSelector("option[value='/admin/db-permission?table=demo_tbls']"))->click();



        sss(2);

        $slInput0 = "//input[@name='id[readOnly]']";
        $valInput0 = clsTestBase2::findOneByXPath($slInput0)->getAttribute('value');
        $slStatus0 = "//i[@data-name='id[readOnly]']";
        clsTestBase2::findOneByXPath($slStatus0)->click();

        $slInput1 = "//input[@name='user_id[readOnly]']";
        $valInput1 = clsTestBase2::findOneByXPath($slInput1)->getAttribute('value');
        $slStatus1 = "//i[@data-name='user_id[readOnly]']";
        clsTestBase2::findOneByXPath($slStatus1)->click();


        sss(2);

        $browser->refresh();

        $valAfter0 = clsTestBase2::findOneByXPath($slInput0)->getAttribute('value');
        $valAfter1 = clsTestBase2::findOneByXPath($slInput1)->getAttribute('value');

        $this->assertTrue($valInput0 != $valAfter0);
        $this->assertTrue($valInput1 != $valAfter1);

        //Trở lại value ban đầu
        clsTestBase2::findOneByXPath($slStatus0)->click();
        clsTestBase2::findOneByXPath($slStatus1)->click();

        //data-name="id[readOnly]"
    }
}
