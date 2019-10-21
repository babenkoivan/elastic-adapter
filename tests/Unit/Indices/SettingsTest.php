<?php
declare(strict_types=1);

namespace ElasticAdaptor\Tests\Unit\Indices;

use BadMethodCallException;
use ElasticAdaptor\Indices\Settings;
use ElasticAdaptor\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdaptor\Indices\Settings
 * @uses   \ElasticAdaptor\Support\Str
 */
class SettingsTest extends TestCase
{
    /**
     * @return array
     */
    public function callParametersProvider(): array
    {
        return [
            ['index', []],
            ['index', [['number_of_replicas' => 2]]],
            ['index', [['number_of_replicas' => 2], ['refresh_interval' => -1]]],
            ['analysis', [['analyzer' => ['content' => ['type' => 'custom', 'tokenizer' => 'whitespace']]]]],
            ['caseSensitiveOption', ['value']],
        ];
    }

    /**
     * @dataProvider callParametersProvider
     * @testdox Test $method option magic setter
     * @param string $method
     * @param array $arguments
     */
    public function test_option_magic_setter(string $method, array $arguments): void
    {
        if (count($arguments) == 0 || count($arguments) > 1) {
            $this->expectException(BadMethodCallException::class);
        }

        $settings = (new Settings())->$method(...$arguments);

        $this->assertSame([
            Str::toSnakeCase($method) => $arguments[0]
        ], $settings->toArray());
    }

    public function test_default_array_conversion(): void
    {
        $this->assertSame([], (new Settings())->toArray());
    }

    public function test_configured_array_conversion(): void
    {
        $settings = (new Settings())
            ->index([
                'number_of_replicas' => 2,
                'refresh_interval' => -1
            ])
            ->analysis([
                'analyzer' => [
                    'content' => [
                        'type' => 'custom',
                        'tokenizer' => 'whitespace'
                    ]
                ]
            ]);

        $this->assertSame([
            'index' => [
                'number_of_replicas' => 2,
                'refresh_interval' => -1
            ],
            'analysis' => [
                'analyzer' => [
                    'content' => [
                        'type' => 'custom',
                        'tokenizer' => 'whitespace'
                    ]
                ]
            ]
        ], $settings->toArray());
    }
}
