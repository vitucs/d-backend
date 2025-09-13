<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 * @cooperative
 * @runInSeparateProcess
 */
class ExampleTest extends TestCase
{
    public function testExample()
    {
        $response = $this->get('/');
        $response->assertStatus(404);
    }
}
