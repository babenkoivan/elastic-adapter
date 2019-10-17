<?php
declare(strict_types=1);

namespace ElasticAdaptor\Support;

use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdaptor\Support\Str
 */
class StrTest extends TestCase
{
    /**
     * @return array
     */
    public function stringProvider(): array
    {
        return [
            ['fooBar', 'foo_bar'],
            ['foo_bar', 'foo_bar'],
            ['_fooBar', '_foo_bar'],
            ['To_fooBar', 'to_foo_bar'],
            ['To__foo_Bar', 'to__foo_bar'],
        ];
    }

    /**
     * @dataProvider stringProvider
     * @testdox String $input can be converted to snake case $output
     * @param string $input
     * @param string $output
     */
    public function test_string_can_be_converted_to_snake_case(string $input, string $output): void
    {
        $this->assertSame($output, Str::toSnakeCase($input));
    }
}