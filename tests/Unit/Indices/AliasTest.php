<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use Elastic\Adapter\Indices\Alias;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Indices\Alias
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
