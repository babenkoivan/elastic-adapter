<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use Elastic\Adapter\Indices\IndexBlueprint;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Indices\IndexBlueprint
 *
 * @uses   \Elastic\Adapter\Indices\Mapping
 */
class IndexBlueprintTest extends TestCase
{
    public function test_index_default_values(): void
    {
        $index = new IndexBlueprint('foo');

        $this->assertNull($index->settings());
        $this->assertNull($index->mapping());
    }

    public function test_index_getters(): void
    {
        $mapping = new Mapping();
        $settings = new Settings();
        $index = new IndexBlueprint('foo', $mapping, $settings);

        $this->assertSame('foo', $index->name());
        $this->assertSame($mapping, $index->mapping());
        $this->assertSame($settings, $index->settings());
    }
}
