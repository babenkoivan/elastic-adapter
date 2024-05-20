<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Extensions;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class BypassFinals implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters
    ): void {
        \DG\BypassFinals::enable(false);
    }
}
