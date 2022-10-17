<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Tests\Unit\Indices;

use OpenSearch\Adapter\Indices\Alias;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OpenSearch\Adapter\Indices\Alias
 */
final class AliasTest extends TestCase
{
    public function test_alias_getters(): void
    {
        $alias = new Alias('2030', true, ['term' => ['year' => 2030]], 'year');

        $this->assertSame('2030', $alias->name());
        $this->assertTrue($alias->isWriteIndex());
        $this->assertSame(['term' => ['year' => 2030]], $alias->filter());
        $this->assertSame('year', $alias->routing());
    }
}
