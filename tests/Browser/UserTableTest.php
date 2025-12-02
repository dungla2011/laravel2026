<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

class UserTableTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testUserAccessTable()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $browser->visit('/admin/user-api');
        $browser->assertSee('admin');

        sss(3);
        $sl = "(//input[@data-field='email'])[1]";
        $val = clsTestBase2::findOneByXPath($sl)->getAttribute('value');
        $this->assertStringContainsString('@', $val);
        //        dump($val);
        //
        //        sss(10);
    }

    /**
     * Tìm user đầu tiên trong db, rồi search userid xem UI có trả lại đúng ko
     */
    public function testSearchOneUser()
    {

        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $user = User::where('email', 'admin@abc.com')->first()->toArray();

        $ometa = User::getMetaObj();

        $urI = $ometa->getUrlSearchField($ometa->getSNameFromField('id'), $user['id']);

        dump('URL S: '.$urI);
        $browser->visit("/admin/user-api$urI");

        sss(2);

        $sl = "(//input[@data-field='email'])[1]";
        $val = clsTestBase2::findOneByXPath($sl)->getAttribute('value');
        $this->assertStringContainsString($user['email'], $val);

        //        sss(10);
    }
}
