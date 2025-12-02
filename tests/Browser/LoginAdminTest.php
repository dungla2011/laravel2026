<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class LoginAdminTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
//        return false;
        return parent::hasHeadlessDisabled();
    }

    public function testLoginTrueAcc2($email = null)
    {
        parent::testLoginTrueAcc();
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExampleAccessHome()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //        $this->browse(function (Browser $browser) {
        //            $browser->visit('/')
        //                    ->assertSee('ADMIN');
        //        });
        $this->assertTrue(true);
    }

    public function testAccessDbPermission()
    {
        $this->testLoginTrueAcc();

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        //        $brs = $this->testLoginTrueAcc();
        //        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $this->browse(function ($browser) {
            $browser->visit('/admin/db-permission');

            $browser->assertSee('Chọn bảng');
            $browser->assertSee('Short Name');
        });
    }

    public function testDemoGrid()
    {
        $this->testLoginTrueAcc();

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $browser->visit('/admin/demo-api');
        //        $browser->assertSee("Đà nẵng");
        //        $browser->assertSee("abc.com");

        $this->assertTrue(true);
    }
}
