<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use BadMethodCallException;
use Closure;
use Elastic\Adapter\Indices\MappingProperties;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Indices\MappingProperties
 */
final class MappingPropertiesTest extends TestCase
{
    public function parametersProvider(): array
    {
        return [
            [
                'type' => 'geoPoint',
                'name' => 'location',
                'parameters' => [
                    'null_value' => null,
                ],
                'expected' => [
                    'location' => [
                        'type' => 'geo_point',
                        'null_value' => null,
                    ],
                ],
            ],
            [
                'type' => 'text',
                'name' => 'description',
                'parameters' => [
                    'boost' => 1,
                ],
                'expected' => [
                    'description' => [
                        'type' => 'text',
                        'boost' => 1,
                    ],
                ],
            ],
            [
                'type' => 'keyword',
                'name' => 'age',
                'parameters' => null,
                'expected' => [
                    'age' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            [
                'type' => 'object',
                'name' => 'user',
                'parameters' => static function (MappingProperties $properties) {
                    $properties->integer('age');

                    return [
                        'properties' => $properties,
                        'dynamic' => true,
                    ];
                },
                'expected' => [
                    'user' => [
                        'type' => 'object',
                        'properties' => [
                            'age' => [
                                'type' => 'integer',
                            ],
                        ],
                        'dynamic' => true,
                    ],
                ],
            ],
            [
                'type' => 'object',
                'name' => 'user',
                'parameters' => [
                    'properties' => [
                        'age' => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
                'expected' => [
                    'user' => [
                        'type' => 'object',
                        'properties' => [
                            'age' => [
                                'type' => 'keyword',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'object',
                'name' => 'user',
                'parameters' => [
                    'properties' => (new MappingProperties())->keyword('age'),
                ],
                'expected' => [
                    'user' => [
                        'type' => 'object',
                        'properties' => [
                            'age' => [
                                'type' => 'keyword',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'object',
                'name' => 'user',
                'parameters' => null,
                'expected' => [
                    'user' => [
                        'type' => 'object',
                    ],
                ],
            ],
            [
                'type' => 'nested',
                'name' => 'user',
                'parameters' => static function (MappingProperties $properties) {
                    $properties->keyword('age');

                    return [
                        'properties' => $properties,
                        'dynamic' => true,
                    ];
                },
                'expected' => [
                    'user' => [
                        'type' => 'nested',
                        'properties' => [
                            'age' => [
                                'type' => 'keyword',
                            ],
                        ],
                        'dynamic' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param Closure|array $parameters
     *
     * @dataProvider parametersProvider
     * @testdox Test $type property setter
     */
    public function test_property_setter(string $type, string $name, $parameters, array $expected): void
    {
        $actual = (new MappingProperties())->$type($name, $parameters);
        $this->assertEquals($expected, $actual->toArray());
    }

    public function test_exception_is_thrown_when_setter_receives_invalid_number_of_arguments(): void
    {
        $this->expectException(BadMethodCallException::class);
        (new MappingProperties())->text();
    }
}
