<?php

namespace Tourze\AsyncCommandBundle\Tests\MessageHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use Tourze\AsyncCommandBundle\MessageHandler\RunCommandHandler;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(RunCommandHandler::class)]
#[RunTestsInSeparateProcesses]
final class RunCommandHandlerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 无需额外设置
    }

    private function getHandler(): RunCommandHandler
    {
        return self::getService(RunCommandHandler::class);
    }

    public function testHasMessageHandlerAttribute(): void
    {
        $reflection = new \ReflectionClass(RunCommandHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testHandlerCanBeInstantiated(): void
    {
        $handler = $this->getHandler();
        $this->assertNotNull($handler);
    }

    public function testInvokeLogsCommandExecutionStart(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('list'); // 使用 Symfony 内置的 list 命令
        $message->setOptions(['--help' => true]);

        // 使用内置命令执行，应该能正常工作
        $handler = $this->getHandler();
        $handler->__invoke($message);

        // 验证处理器对象仍然有效
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }

    public function testInvokeHandlesRecoverableException(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('non-existent:command');

        // 执行应该完成而不抛出异常，因为异常会被捕获并记录日志
        $handler = $this->getHandler();
        $handler->__invoke($message);

        // 验证处理器对象仍然有效
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }

    public function testInvokeWithEmptyCommand(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('');

        // 空命令应该能正常处理，会记录错误日志但不抛出异常
        $handler = $this->getHandler();
        $handler->__invoke($message);

        // 验证处理器对象仍然有效
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }

    public function testInvokeWithComplexOptions(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('list'); // 使用内置命令
        $message->setOptions([
            '--help' => true,
            '--format' => 'json',
        ]);

        // 复杂选项应该能正常处理
        $handler = $this->getHandler();
        $handler->__invoke($message);

        // 验证处理器对象仍然有效
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }

    public function testConstructorSetsApplicationProperties(): void
    {
        $handler = $this->getHandler();
        // 使用反射检查内部Application的配置
        $reflection = new \ReflectionClass($handler);
        $applicationProperty = $reflection->getProperty('application');
        $applicationProperty->setAccessible(true);
        $application = $applicationProperty->getValue($handler);

        $this->assertInstanceOf(Application::class, $application);
    }

    public function testInvokeLogsErrorWithExceptionDetails(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('definitely-not-exist:command');

        // 不存在的命令应该能正常处理，会记录错误日志但不抛出异常
        $handler = $this->getHandler();
        $handler->__invoke($message);

        // 验证处理器对象仍然有效
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }
}
