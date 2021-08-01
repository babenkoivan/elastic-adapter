<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Indices;

use ElasticAdapter\Indices\IndexBlueprint;
use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Indices\IndexBlueprint
 *
 * @uses   ElasticAdapter\Indices\Mapping
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
