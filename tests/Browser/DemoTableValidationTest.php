<?php

namespace Tests\Feature;

use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTableValidationTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testDemoValidationField()
    {
        //Todo
        $this->assertTrue(true);
    }
}
