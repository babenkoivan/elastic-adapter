<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Support;

use ElasticAdapter\Support\Arr;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Support\Arr
 */
final class ArrTest extends TestCase
{
    public function caseProvider(): array
    {
        return [
            [['product' => ['price' => 10]], 'product', ['price' => 10]],
            [['product' => ['price' => 10]], 'product.price', 10],
            [['order' => ['items' => [['name' => 'first'], ['name' => 'second']]]], 'order.items.0.name', 'first'],
            [['left' => 1], 'right', null],
        ];
    }

    /**
     * @param mixed $item
     *
     * @dataProvider caseProvider
     * @testdox Item with key $key can be retrieved from array
     */
    public function test_item_can_be_received_using_dot_notation(array $array, string $key, $item): void
    {
        $this->assertSame($item, Arr::get($array, $key));
    }
}
