<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use Elastic\Adapter\Indices\Index;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Index::class)]
#[UsesClass(Mapping::class)]
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
