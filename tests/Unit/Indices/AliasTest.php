<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Indices;

use ElasticAdapter\Indices\Alias;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Indices\Alias
 */
final class AliasTest extends TestCase
{
    public function test_alias_getters(): void
    {
        $alias = new Alias('2030', ['term' => ['year' => 2030]], 'year');

        $this->assertSame('2030', $alias->getName());
        $this->assertSame(['term' => ['year' => 2030]], $alias->getFilter());
        $this->assertSame('year', $alias->getRouting());
    }
}
