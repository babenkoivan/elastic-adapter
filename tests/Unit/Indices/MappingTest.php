<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Indices;

use BadMethodCallException;
use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Indices\Mapping
 *
 * @uses   \ElasticAdapter\Support\Str
 */
class MappingTest extends TestCase
{
    public function test_field_names_can_be_disabled(): void
    {
        $mapping = (new Mapping())->disableFieldNames();

        $this->assertSame([
            '_field_names' => [
                'enabled' => false,
            ],
        ], $mapping->toArray());
    }

    public function test_field_names_can_be_enabled(): void
    {
        $mapping = (new Mapping())->enableFieldNames();

        $this->assertSame([
            '_field_names' => [
                'enabled' => true,
            ],
        ], $mapping->toArray());
    }

    public function test_source_can_be_disabled(): void
    {
        $mapping = (new Mapping())->disableSource();

        $this->assertSame([
            '_source' => [
                'enabled' => false,
            ],
        ], $mapping->toArray());
    }

    public function test_source_can_be_enabled(): void
    {
        $mapping = (new Mapping())->enableSource();

        $this->assertSame([
            '_source' => [
                'enabled' => true,
            ],
        ], $mapping->toArray());
    }

    public function callParametersProvider(): array
    {
        return [
            ['boolean', []],
            ['geoPoint', ['foo', ['null_value' => null]]],
            ['text', ['bar', ['boost' => 1], ['store' => true]]],
            ['keyword', ['foobar']],
        ];
    }

    /**
     * @dataProvider callParametersProvider
     * @testdox Test $method property magic setter
     */
    public function test_property_magic_setter(string $method, array $arguments): void
    {
        if (count($arguments) == 0 || count($arguments) > 2) {
            $this->expectException(BadMethodCallException::class);
        }

        $mapping = (new Mapping())->$method(...$arguments);

        $this->assertSame([
            'properties' => [
                $arguments[0] => ['type' => Str::toSnakeCase($method)] + ($arguments[1] ?? []),
            ],
        ], $mapping->toArray());
    }

    public function test_default_array_casting(): void
    {
        $this->assertSame([], (new Mapping())->toArray());
    }

    public function test_configured_array_casting(): void
    {
        $mapping = (new Mapping())
            ->disableFieldNames()
            ->enableSource()
            ->text('foo')
            ->boolean('bar', [
                'boost' => 1,
            ])
            ->dynamicTemplate('integers', [
                'match_mapping_type' => 'long',
                'mapping' => [
                    'type' => 'integer',
                ],
            ]);

        $this->assertSame([
            '_field_names' => [
                'enabled' => false,
            ],
            '_source' => [
                'enabled' => true,
            ],
            'properties' => [
                'foo' => [
                    'type' => 'text',
                ],
                'bar' => [
                    'type' => 'boolean',
                    'boost' => 1,
                ],
            ],
            'dynamic_templates' => [
                [
                    'integers' => [
                        'match_mapping_type' => 'long',
                        'mapping' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ], $mapping->toArray());
    }

    public function test_on_serialize_callback(): void
    {
        $mapping = (new Mapping())
            ->setOnBeforeSerializeCallback(function (array $mapping) {
                $mapping['dynamic'] = false;
                $mapping['date_detection'] = false;

                return $mapping;
            })
            ->text('last_name')
            ->toArray();

        $this->assertSame([
            'properties' => [
                'last_name' => [
                    'type' => 'text'
                ]
            ],
            'dynamic' => false,
            'date_detection' => false
        ], $mapping);
    }

    public function test_can_remove_on_serialize_callback(): void
    {
        $mapping = (new Mapping())
            ->setOnBeforeSerializeCallback(function (array $mapping) {
                $mapping['dynamic'] = false;
                $mapping['date_detection'] = false;

                return $mapping;
            })
            // I changed my mind and don't want a callback anymore
            ->setOnBeforeSerializeCallback()
            ->text('last_name')
            ->toArray();

        $this->assertSame([
            'properties' => [
                'last_name' => [
                    'type' => 'text'
                ]
            ]
        ], $mapping);
    }

    public function test_on_serialize_callback_with_invalid_return(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The custom mapping should be an array');

        (new Mapping())
            ->setOnBeforeSerializeCallback(function (array $mapping) {
                return true;
            })
            ->text('last_name')
            ->toArray();
    }
}
