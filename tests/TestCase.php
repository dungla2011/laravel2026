<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Run migrations
//        $this->artisan('migrate', ['--database' => 'sqlite']);
//        $this->artisan('migrate');

        User::createUserAdminDefault();
        User::createUserMemberForTest();
    }
}
