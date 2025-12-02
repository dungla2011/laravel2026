<?php

namespace Tests\Feature;

use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableUploadImageTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    //Todo: test upload multi image to demo
    public function testDemoUploadImageMulti()
    {
        $this->assertTrue(true);

        return;
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/admin/demo-api');

    }

    //Todo: test nếu readonly imagesList, thì chỉ thấy ảnh mà ko move, xóa ảnh được

}
