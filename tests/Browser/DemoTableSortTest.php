<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableSortTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    //Kiểm tra Sort 1 field
    public function testSortField($field = 'number1')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $mm = DemoTbl::orderBy($field, 'desc')->get()->toArray();

        $biggest = $mm[0];
        $smallest = end($mm);

        dump($biggest);
        dump($smallest);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();

        $sname = $objMeta->getShortNameFromField($field);

        
        // sleep(30);
        $elm = clsTestBase2::findOneByXPath("//a[@data-tester='sort_field_$field']");
        //Kiem tra elm ton tai
        $this->assertTrue($elm != null);

        $elm->click();
        

        sleep(1);
        $url = $drv->getCurrentURL();
        $paramCheck = DEF_PREFIX_SORTBY_URL_PARAM_GLX."$sname=desc";
        dump("URLx = $url");
        dump("paramCheck = $paramCheck, sort_field_$field");
        $this->assertTrue(strstr($url, $paramCheck) != false);

        //Phần tử lớn nhất
        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL big1 = $val");
        $this->assertTrue($val == $biggest[$field]);
        //Phần tử lớn nhì
        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']", 1)->getAttribute('value');
        dump("VAL big2 = $val");
        $this->assertTrue($val == $mm[1][$field]);

        clsTestBase2::findOneByXPath("//a[@data-tester='sort_field_$field']")->click();

        $url = $drv->getCurrentURL();

//        sss(10);

        $paramCheck = DEF_PREFIX_SORTBY_URL_PARAM_GLX."$sname=asc";
        dump("URLx = $url");
        dump("paramCheck = $paramCheck");
        $this->assertTrue(strstr($url, $paramCheck) != false);

        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL small = $val");
        $this->assertTrue($val == $smallest[$field]);

    }

    public function testSortSomeField()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        self::testSortField('textarea1');
//        self::testSortField('string2');
        self::testSortField('user_id');

    }
}
