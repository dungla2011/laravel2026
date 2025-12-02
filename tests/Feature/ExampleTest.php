<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example123()
    {
        $response = $this->get('/');

        $url = request()->fullUrl();
        //        echo "<br/>\n BaseURl3 : $url";

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($response->content());
        //        echo "</pre>";

        $response->assertStatus(200);
    }
}
