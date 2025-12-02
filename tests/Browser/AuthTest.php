<?php

namespace Tests\Browser;

use App\Models\User;

class AuthTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return parent::hasHeadlessDisabled();
    }

    public function testLoginTrueAcc5($email = null)
    {
        parent::testLoginTrueAcc();
    }

    //Todo test login, reg, resetpw
    /**
     * Với user login với mạng xh, thì ko có pw, khi đó sẽ có nút reset pw trong member (vì pw trống)
     */
}
