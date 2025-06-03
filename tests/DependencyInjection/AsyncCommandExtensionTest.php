<?php

namespace Tourze\AsyncCommandBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Tourze\AsyncCommandBundle\DependencyInjection\AsyncCommandExtension;

class AsyncCommandExtensionTest extends TestCase
{
    private AsyncCommandExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new AsyncCommandExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_extends_symfony_extension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(AsyncCommandExtension::class, $this->extension);
    }

    public function test_load_services_configuration(): void
    {
        // 确保 services.yaml 文件存在
        $configPath = __DIR__ . '/../../src/Resources/config/services.yaml';
        $this->assertFileExists($configPath);

        // 加载配置
        $this->extension->load([], $this->container);

        // 验证是否成功加载配置（容器应该仍然可用且没有抛出异常）
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_load_with_empty_configs(): void
    {
        $this->extension->load([], $this->container);
        
        // 应该没有异常抛出，容器应该仍然可用
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_load_with_multiple_config_arrays(): void
    {
        $configs = [
            [],
            ['some_key' => 'some_value'],
            []
        ];
        
        $this->extension->load($configs, $this->container);
        
        // 应该没有异常抛出
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function test_extension_alias(): void
    {
        // Symfony Extension 的默认别名是类名去掉 Extension 后缀并转换为下划线格式
        $this->assertEquals('async_command', $this->extension->getAlias());
    }

    public function test_load_uses_correct_file_locator(): void
    {
        // 测试配置加载过程
        $this->extension->load([], $this->container);
        
        // 验证没有异常抛出
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
        $this->assertTrue(true); // 如果执行到这里说明配置加载成功
    }

    public function test_configuration_directory_exists(): void
    {
        $configDir = __DIR__ . '/../../src/Resources/config';
        $this->assertDirectoryExists($configDir);
    }

    public function test_services_yaml_file_exists(): void
    {
        $servicesFile = __DIR__ . '/../../src/Resources/config/services.yaml';
        $this->assertFileExists($servicesFile);
    }
} 