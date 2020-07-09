<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Support;

use ElasticAdapter\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Support\Str
 */
class StrTest extends TestCase
{
    public function stringProvider(): array
    {
        return [
            ['fooBar', 'foo_bar'],
            ['foo_bar', 'foo_bar'],
            ['_fooBar', '_foo_bar'],
            ['To_fooBar', 'to_foo_bar'],
            ['To__foo_Bar', 'to__foo_bar'],
            ['foo1Bar', 'foo1_bar'],
            ['foo-Bar', 'foo_bar'],
            ['to-fooBar', 'to_foo_bar'],
            ['FooBar', 'foo_bar'],
            ['FOOBar', 'foobar'],
            ['fooBAR', 'foo_bar'],
        ];
    }

    /**
     * @dataProvider stringProvider
     * @testdox String $input can be converted to snake case $output
     */
    public function test_string_can_be_converted_to_snake_case(string $input, string $output): void
    {
        $this->assertSame($output, Str::toSnakeCase($input));
    }
}
