<?php return '<?php

namespace MyModulesDir\\MYMODULE\\Tests\\Feature;

use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Illuminate\\Foundation\\Testing\\WithFaker;
use Tests\\TestCase;

class MyNewTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get(\'/\');

        $response->assertStatus(200);
    }
}
';