<?php

namespace Tests\Feature;

use App\Models\User;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

class MemberAccessSomeUrlTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        //        return true;
        return parent::hasHeadlessDisabled();
    }

    /**
     * Chắc chắn User member vào được các url, ko bị lỗi
     */
    public function testMemberAccessSomeWebUrl()
    {

        //Todo: need testFile folder

        $this->testLoginTrueAcc('member@abc.com');

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $mUrlWeb = [
            '/member/file',
            '/member/folder-file',
        ];
        foreach ($mUrlWeb as $link) {
            //Chắc chắn là Get được link:
            $linkTrash = $link.'?in_trash=1';

            $browser->visit($link);
            $browser->assertSee('Copyright');

            $browser->visit($linkTrash);
            $browser->assertSee('Copyright');

            //            self::assertTrue($ret->status(), 200);
        }

    }
}
