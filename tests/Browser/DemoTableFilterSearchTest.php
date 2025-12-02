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
class DemoTableFilterSearchTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    //Kiểm tra các ô input hiện ra với các trường được phép
    public function testFilterInputWithUserGroup()
    {
        //search_top_grid
        $this->testLoginTrueAcc(1);
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        $objMeta = DemoTbl::getMetaObj();

        $mm = $drv->findElements(WebDriverBy::xpath("//input[contains(@class, 'search_top_grid')]"));
        $mFieldFound = [];
        if ($mm) {
            foreach ($mm as $elm) {
                $f = $elm->getAttribute('data-field-s');
                echo "<br/>\n field = ".$f;
                $mFieldFound[] = $f;
            }
        }

        $mFieldSearchDb = $objMeta->getListFilterField(DEF_GID_ROLE_MEMBER);
        $mFieldSearchDb = $objMeta->getListFilterField(1);

        echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
        print_r($mFieldSearchDb);
        echo '</pre>';
        echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
        print_r($mFieldFound);
        echo '</pre>';
        $mFieldSearchDb = array_filter($mFieldSearchDb);
        $mFieldFound = array_filter($mFieldFound);

        $dif = array_diff($mFieldSearchDb, $mFieldFound);
        echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
        print_r($dif);
        echo '</pre>';
        self::assertTrue(! $dif);
        $dif1 = array_diff($mFieldFound, $mFieldSearchDb);
        echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
        print_r($dif1);
        echo '</pre>';
        self::assertTrue(! $dif1);

    }

    public function testSearchUseridEqual($field = 'user_id')
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $mm = User::all()->toArray();

        $this->assertTrue(count($mm) > 1);

        $user = $mm[1];

        $mDemo = DemoTbl::where('user_id', '=', $user['id'])->get();

        dump('COUNT mDEMO = '.count($mDemo));

        $this->assertTrue(count($mDemo) >= 1);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        $objMeta = DemoTbl::getMetaObj();

        $sname = $objMeta->getShortNameFromField($field);

        //        clsTestBase2::findOneByXPath("//input[@name='seby_$sname']")->sendKeys($user['id']);//->sendKeys(WebDriverKeys::ENTER);
        $drv->executeScript("document.querySelector('input[name=\"seby_$sname\"]').value='".$user['id']."'");
        $drv->findElement(WebDriverBy::id('search_btn_top'))->click();

        sss(1);

        $url = $drv->getCurrentURL();
        $paramCheck = DEF_PREFIX_SEARCH_URL_PARAM_GLX."$sname=".$user['id'];
        dump("URLx = $url");
        dump("paramCheck = $paramCheck");
        $this->assertTrue(strstr($url, $paramCheck) != false);

        //        sss(100);
        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL Uid = $val");
        $this->assertTrue($val == $user['id']);
    }

    public function testSearchUseridGreater1($field = 'user_id')
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //$mm = User::orderBy('id','desc')->get()->toArray();

        DB::enableQueryLog();
        $mDemo = DemoTbl::orderBy('user_id', 'asc')->where('user_id', '>', 0)->distinct()->get(['user_id'])->toArray();

        dump(DB::getQueryLog());

        dump($mDemo);
        $this->assertTrue(count($mDemo) > 1);

        //Lấy ra 2 UID
        $minUid = $mDemo[0]['user_id'];
        $nextUid = $mDemo[1]['user_id'];

        dump("Min, next = $minUid, $nextUid");

        $this->assertTrue($minUid > 0);

        //Lấy ra user thứ 2, để có id, sau đó tìm userid lớn hơn user thứ 2 đó, sẽ là userid thứ nhất
        $userNext = User::find($nextUid)->toArray();
        $userMin = User::find($minUid)->toArray();

        //Chắc chắn là trong DEMO có data ok, là có userid thứ nhất
        //        $mDemo = DemoTbl::where('user_id', $user1['id'])->orderBy('user_idx','asc')->get();
        //        dump("COUNT mDEMO = " . count($mDemo));
        //        $this->assertTrue( count($mDemo) >= 1);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        $objMeta = DemoTbl::getMetaObj();

        $sname = $objMeta->getShortNameFromField($field);

        clsTestBase2::findOneByXPath("//select[@name='".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."']")->findElement(WebDriverBy::cssSelector("option[value='gt']"))->click();

        $namex = DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname;
        dump('Namx = '.$namex);
        $drv->executeScript("document.querySelector('input[name=\"$namex\"]').value='".$userMin['id']."'");
        //        clsTestBase2::findOneByXPath("//input[@name='".DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname."']")->sendKeys($userMin['id'])->sendKeys(WebDriverKeys::ENTER);

        $drv->findElement(WebDriverBy::id('search_btn_top'))->click();

        $url = $drv->getCurrentURL();

        //        sss(100);

        $paramCheck = DEF_PREFIX_SEARCH_URL_PARAM_GLX."$sname=".$userMin['id'];
        dump("URLx = $url");
        dump("paramCheck = $paramCheck");
        $this->assertTrue(strstr($url, $paramCheck) != false);

        $paramCheck = DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX."$sname=gt";

        $this->assertTrue(strstr($url, $paramCheck) != false);

        $valSelect = clsTestBase2::findOneByXPath("//select[@name='".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."']")->getAttribute('value');
        dump("VAl SELECT = $valSelect");
        $this->assertTrue($valSelect == 'gt');

        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL Uid = $val");
        $this->assertTrue($val == $userNext['id']);

    }

    /**
     * Update time mới cho 1 bản ghi, sau đó tìm time mới đó, với greater > time mới - 1 giây
     */
    public function testSearchDateGreater()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $mm = DemoTbl::all();
        $m1 = $mm[1];
        $timeUpdate = nowyh();
        $timeSmaller = nowyh(time() - 1);
        $oldTime = $m1->created_at;
        $m1->created_at = $timeUpdate;
        $m1->update();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        $objMeta = DemoTbl::getMetaObj();
        $field = 'created_at';
        $sname = $objMeta->getShortNameFromField($field);

        clsTestBase2::findOneByXPath("//select[@name='".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."']")->findElement(WebDriverBy::cssSelector("option[value='gt']"))->click();
        clsTestBase2::findOneByXPath("//input[@name='".DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname."']")->sendKeys($timeSmaller)->sendKeys(WebDriverKeys::ENTER);

        dump("VAL field = $field");
        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL TimeFound = $val");
        $this->assertTrue($val == $timeUpdate);

        //Trở lại giá trị cũ:
        $m1->created_at = $oldTime;
        $m1->update();

    }

    /**
     * Update time mới cho 1 bản ghi, sau đó tìm LIKE time mới
     */
    public function testSearchLikeFilter()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $mm = DemoTbl::all();
        $m1 = $mm[1];
        $timeUpdate = nowyh();
        //Bỏ đầu, đuôi đi 1 ký tự để tìm
        $timeSearchLike = substr($timeUpdate, 1, -1);
        $oldTime = $m1->created_at;
        $m1->created_at = $timeUpdate;
        $m1->update();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;
        $this->openFilterButton();

        $objMeta = DemoTbl::getMetaObj();
        $field = 'created_at';
        $sname = $objMeta->getShortNameFromField($field);

        clsTestBase2::findOneByXPath("//select[@name='".DEF_PREFIX_SEARCH_OPT_URL_PARAM_GLX.$sname."']")->findElement(WebDriverBy::cssSelector("option[value='C']"))->click();
        clsTestBase2::findOneByXPath("//input[@name='".DEF_PREFIX_SEARCH_URL_PARAM_GLX.$sname."']")->sendKeys($timeSearchLike)->sendKeys(WebDriverKeys::ENTER);

        $val = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value');
        dump("VAL TimeFound = $val");
        $this->assertTrue($val == $timeUpdate);

        //Trở lại giá trị cũ:
        $m1->created_at = $oldTime;
        $m1->update();

        //        sss(10);
    }

    /**
     * Filter userid autocomplete: gõ 1 text vào để auto complete sổ xuống, click vào đó, và click Filter Search, tìm 1 phần tử  userid, xem có đúng text, và ID không
     */
    public function testFilterDemoAutoCompleteSingleValue()
    {

        $user = User::where('email', 'admin@abc.com')->first();
        $mail = $user->email;
        $demo = DemoTbl::where('user_id', $user->id)->first();

        $this->assertTrue($demo != null, "Need insert one record have userid = $uid");

        $this->testLoginTrueAcc();

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');

        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        //data-autocomplete-id="filter_field_user_id"
        sss(1);
        clsTestBase2::findOneByXPath("//input[@data-autocomplete-id='filter_field_user_id']")->sendKeys("$mail");

        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();
        $drv->findElement(WebDriverBy::id('search_btn_top'))->click();
        $found = (clsTestBase2::findOneByXPath("//span[contains(@class, 'span_auto_complete')][contains(text(),'$mail')]"));
        $this->assertNotNull($found);
        $val = $found->getAttribute('data-item-value');
        $this->assertTrue($uid == $val);
        //        sss(10);
    }

    /**
     * Filter taglist autocomplete: gõ 1 text vào để auto complete sổ xuống, click vào đó, và click Filter Search,
     * tìm 1 phần tử  tag_list_id, xem có đúng text, và ID không
     */
    public function testFilterDemoAutoCompleteMultiValue()
    {
        //filter_field_tag_list_id

        $obj = DemoTbl::where('tag_list_id', '!=', null)->first()->toArray();

        dump($obj);

        $tagId1 = explode(',', $obj['tag_list_id'])[0];

        $tag = TagDemo::where('id', $tagId1)->first()->toArray();
        $name = $tag['name'];

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');
        clsTestBase2::$driver = $drv;

        $this->openFilterButton();

        clsTestBase2::findOneByXPath("//input[@data-autocomplete-id='filter_field_tag_list_id']")->sendKeys("$name");
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();
        $drv->findElement(WebDriverBy::id('search_btn_top'))->click();

        sss(1);
        $this->openFilterButton();

        sss(1);

        //Có chữ name trên Filter
        $found = (clsTestBase2::findOneByXPath("//input[contains(@class, 'search_top_grid')][@data-autocomplete-id='filter_field_tag_list_id'][contains(@value,'$name')]"));
        $this->assertNotNull($found, "Not found any contain '$name'");

        //Todo:
         return
         
        //Có chữ name trong 1 bản ghi
        $found = (clsTestBase2::findOneByXPath("//span[@data-item-value='$tagId1'][contains(@data-autocomplete-id, 'tag_list_id')][contains(text(),'$name')]"));
        $this->assertNotNull($found, "Not found any contain '$name'");
        $val = $found->getAttribute('data-item-value');

        dump("VAL = $val");

        $this->assertTrue($tagId1 == $val);

    }

    /**
     * Test button status Filter, xem màu xanh đỏ, và hàng đầu tiên có status 1 hay không khi đã filter
     */
    public function testStatusFilterButton($field = 'status')
    {

        $obj = DemoTbl::where("$field", 1)->first()->toArray();

        $this->assertNotNull($obj);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();

        $key = $objMeta->getSearchKeyField($field);
        $browser->visit("/admin/demo-api?$key=1");

        $this->openFilterButton();

        $found = clsTestBase2::findOneByXPath("//input[@data-field='$field'][@value='1']");
        $this->assertNotNull($found);

        $found = clsTestBase2::findOneByXPath("//a[@class='filter_status_on_status'][contains(@style, 'red')]");
        $this->assertNotNull($found);
        $found = clsTestBase2::findOneByXPath("//a[@class='filter_status_off_status'][not(contains(@style, 'red'))]");
        $this->assertNotNull($found);

        //Về status 0
        $browser->visit("/admin/demo-api?$key=0");
        $found = clsTestBase2::findOneByXPath("//a[@class='filter_status_off_status'][contains(@style, 'red')]");
        $this->assertNotNull($found);
        $found = clsTestBase2::findOneByXPath("//a[@class='filter_status_on_status'][not(contains(@style, 'red'))]");
        $this->assertNotNull($found);

        //        sss(10);

    }
}
