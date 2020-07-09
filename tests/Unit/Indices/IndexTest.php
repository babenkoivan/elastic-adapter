<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Indices;

use ElasticAdapter\Indices\Index;
use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Indices\Index
 */
class IndexTest extends TestCase
{
    public function test_index_default_values(): void
    {
        $index = new Index('foo');

        $this->assertNull($index->getSettings());
        $this->assertNull($index->getMapping());
    }

    public function test_index_getters(): void
    {
        $mapping = new Mapping();
        $settings = new Settings();
        $index = new Index('foo', $mapping, $settings);

        $this->assertSame('foo', $index->getName());
        $this->assertSame($mapping, $index->getMapping());
        $this->assertSame($settings, $index->getSettings());
    }
}
