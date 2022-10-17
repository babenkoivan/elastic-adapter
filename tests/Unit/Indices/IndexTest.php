<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Tests\Unit\Indices;

use OpenSearch\Adapter\Indices\Index;
use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OpenSearch\Adapter\Indices\Index
 *
 * @uses   \OpenSearch\Adapter\Indices\Mapping
 */
class IndexTest extends TestCase
{
    public function test_index_default_values(): void
    {
        $index = new Index('foo');

        $this->assertNull($index->settings());
        $this->assertNull($index->mapping());
    }

    public function test_index_getters(): void
    {
        $mapping = new Mapping();
        $settings = new Settings();
        $index = new Index('foo', $mapping, $settings);

        $this->assertSame('foo', $index->name());
        $this->assertSame($mapping, $index->mapping());
        $this->assertSame($settings, $index->settings());
    }
}
