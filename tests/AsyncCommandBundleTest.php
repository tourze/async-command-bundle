<?php

declare(strict_types=1);

namespace Tourze\AsyncCommandBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\AsyncCommandBundle\AsyncCommandBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(AsyncCommandBundle::class)]
#[RunTestsInSeparateProcesses]
final class AsyncCommandBundleTest extends AbstractBundleTestCase
{
}
