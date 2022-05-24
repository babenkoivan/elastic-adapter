<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Documents;

use Elastic\Adapter\Documents\Routing;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Documents\Routing
 */
final class RoutingTest extends TestCase
{
    public function test_routing_values_can_be_added_and_retrieved(): void
    {
        $routing = (new Routing())
            ->add('1', 'user1')
            ->add('2', 'user2');

        $this->assertTrue($routing->has('1'));
        $this->assertSame('user1', $routing->get('1'));
        $this->assertTrue($routing->has('2'));
        $this->assertSame('user2', $routing->get('2'));
        $this->assertFalse($routing->has('3'));
        $this->assertNull($routing->get('3'));
    }
}
