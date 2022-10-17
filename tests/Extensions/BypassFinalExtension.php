<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Tests\Extensions;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

final class BypassFinalExtension implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
