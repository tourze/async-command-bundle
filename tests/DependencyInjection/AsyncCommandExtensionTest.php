<?php

declare(strict_types=1);

namespace Tourze\AsyncCommandBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\AsyncCommandBundle\DependencyInjection\AsyncCommandExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(AsyncCommandExtension::class)]
final class AsyncCommandExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private AsyncCommandExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new AsyncCommandExtension();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AsyncCommandExtension::class, $this->extension);
    }

    public function testGetAlias(): void
    {
        $this->assertEquals('async_command', $this->extension->getAlias());
    }

    public function testExtensionLoadsServices(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $this->extension->load([], $container);

        // 验证扩展加载没有抛出异常
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);
    }
}
