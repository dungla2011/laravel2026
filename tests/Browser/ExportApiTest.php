<?php

namespace Tests\Feature;

use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class ExportApiTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testExportApi()
    {
        dump('=== Check '.get_called_class().'/'.__FUNCTION__);
        if (! $browser = $this->getBrowserLogined()) {
        }
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        $browser->visit('/api-export?id=3');

        $browser->assertSee('Auto Generate Api Document');
        $browser->assertDontSee('Auto 123 Generate Api Document');

    }
}
