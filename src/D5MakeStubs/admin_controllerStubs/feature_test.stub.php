<?php

namespace Tests\Feature\Admin\DummyBaseTest;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DummyTest extends FeatureTestBase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $id = $this->id;
        $uri = "DummyUri";
        $response = $this
            ->actingAs($this->actionAdmin(),'admin')
            ->DummyMethod($uri,[]);

        $response->assertStatus(200);
    }
}
