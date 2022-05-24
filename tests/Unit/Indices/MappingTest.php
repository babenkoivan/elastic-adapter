<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use Elastic\Adapter\Indices\Mapping;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Indices\Mapping
 *
 * @uses   \Elastic\Adapter\Indices\MappingProperties
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
}
