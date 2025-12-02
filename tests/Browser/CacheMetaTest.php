<?php

namespace Tests\Feature;

use LadLib\Common\Database\MetaOfTableInDb;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class CacheMetaTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function testMetaDataApi()
    {
        //Todo: test xem có hoạt động ko, file có được ghi và có được dùng ko
        //MetaOfTableInDb::getMetaInfoFromCache...

        $this->assertTrue(true);
    }
}
