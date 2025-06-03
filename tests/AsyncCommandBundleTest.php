<?php

namespace Tourze\AsyncCommandBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\AsyncCommandBundle\AsyncCommandBundle;

class AsyncCommandBundleTest extends TestCase
{
    public function test_extends_symfony_bundle(): void
    {
        $bundle = new AsyncCommandBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function test_can_be_instantiated(): void
    {
        $bundle = new AsyncCommandBundle();
        $this->assertInstanceOf(AsyncCommandBundle::class, $bundle);
    }

    public function test_bundle_name(): void
    {
        $bundle = new AsyncCommandBundle();
        $this->assertEquals('AsyncCommandBundle', $bundle->getName());
    }

    public function test_bundle_path(): void
    {
        $bundle = new AsyncCommandBundle();
        $path = $bundle->getPath();
        $this->assertStringEndsWith('/src', $path);
        $this->assertDirectoryExists($path);
    }

    public function test_bundle_namespace(): void
    {
        $bundle = new AsyncCommandBundle();
        $this->assertEquals('Tourze\AsyncCommandBundle', $bundle->getNamespace());
    }
} 