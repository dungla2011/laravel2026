<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableRiskTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    /**
     * Kiểm tra Risk: ở trang index, khi thay đổi 1 item, click Save All, thì mọi thứ không thay đổi phải được giữ nguyên
     * (DB kiểm tra ko thay đổi, có thể là trừ Field: modified_at và log nếu có)
     */
    public function testRiskDemoSaveAllKeepAllData()
    {
        //tttttt

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $mm = DemoTbl::orderBy('id', 'desc')->limit(5)->get()->toArray();

        $n = 5;

        self::assertTrue(count($mm) == $n, "chắc chắn có $n bản ghi để test");

        $browser->visit("/admin/demo-api?limit=$n");

        $textChange = microtime(1);
        $id0 = $mm[0]['id'];
        $input = clsTestBase2::findOneByXPath("//input[@data-field='textarea1'][@data-id='$id0']");
        $input->clear()->sendKeys($textChange);

        $drv->findElement(WebDriverBy::id('save-all-data'))->click();
        sss(2);

        //        $drv->navigate()->refresh();
        //        sss(2);

        $mm2 = DemoTbl::orderBy('id', 'desc')->limit(5)->get()->toArray();

        for ($i = 0; $i < count($mm); $i++) {
            $elm = $mm[$i];
            $elm1 = $mm2[$i];
            $dif = array_diff($elm1, $elm);

            dump(" I  = $i");

            dump($dif);

            unset($dif['updated_at']);

            if ($i > 0) {
                self::assertTrue(! $dif, ' từ phần tử thứ 2 trở đi, ko có gì thay đổi');
            }

            if ($dif) {
                dump($elm);
                dump($elm1);
                dump('DIF ID: '.$elm['id']);

                dump($dif['textarea1']);
                dump($textChange);

                self::assertTrue($elm['id'] == $id0);
                self::assertTrue(trim($dif['textarea1']) == trim($textChange), "'".$dif['textarea1']."'"." vs '$textChange'");
            }

        }

    }
}
