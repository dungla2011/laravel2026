<?php

namespace Tests\Feature;

use Tests\Browser\DuskTestCaseBase;

class DemoTableTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return parent::hasHeadlessDisabled();
    }

    public function testDemo1AccessTable()
    {

        $this->testLoginTrueAcc();

        $browser = $this->getBrowserLogined();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $browser->visit('/admin/demo-api');
        $browser->assertSee('Đà nẵng');
        $browser->assertSee('abc.com');
        //
        //        $browser->assertSeeIn('textarea1[]', '66');

        $sl = 'input[data-autocomplete-id="47-textarea1"]';
        //First input has class textarea1
        $sl = '.textarea1';

        //OK1
        $browser->assertAttributeContains('input[id=x222]', 'value', 111);
        //OK2
        $browser->assertAttributeContains($sl, 'value', 66);
        //OK3
        //$browser->type('input[id=x222]','xxxx');

        $randomVal = time();
        //Nhập vào 1 số và enter
        $browser->type($sl, $randomVal);
        $browser->keys($sl, '{enter}');
        $browser->refresh();
        //Sau khi refresh, xem số đó đã được ghi vào db chưa:
        $browser->assertAttributeContains($sl, 'value', $randomVal);

        sss(1);
    }
}
