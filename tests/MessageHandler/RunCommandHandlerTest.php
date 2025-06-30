<?php

namespace Tourze\AsyncCommandBundle\Tests\MessageHandler;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use Tourze\AsyncCommandBundle\MessageHandler\RunCommandHandler;

class RunCommandHandlerTest extends TestCase
{
    private $kernel;
    private $logger;
    private RunCommandHandler $handler;

    protected function setUp(): void
    {
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        /** @var KernelInterface&\PHPUnit\Framework\MockObject\MockObject $kernel */
        $kernel = $this->kernel;
        /** @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject $logger */
        $logger = $this->logger;

        $this->handler = new RunCommandHandler(
            $kernel,
            $logger
        );
    }

    public function test_has_message_handler_attribute(): void
    {
        $reflection = new \ReflectionClass(RunCommandHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);
        
        $this->assertCount(1, $attributes);
    }

    public function test_handler_can_be_instantiated(): void
    {
        $this->assertInstanceOf(RunCommandHandler::class, $this->handler);
    }

    public function test_handler_can_be_instantiated_without_logger(): void
    {
        $handler = new RunCommandHandler($this->kernel);
        $this->assertInstanceOf(RunCommandHandler::class, $handler);
    }

    public function test_invoke_logs_command_execution_start(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('test:command');
        $message->setOptions(['--env' => 'test']);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('准备执行命令', [
                'command' => 'test:command',
                'options' => ['--env' => 'test'],
            ]);

        // 模拟一个简单的命令执行，我们需要让Application抛出异常来测试日志记录
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('异步执行命令时发生异常'),
                $this->arrayHasKey('command')
            );

        $this->handler->__invoke($message);
    }

    public function test_invoke_handles_recoverable_exception(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('failing:command');

        // 由于当前实现会捕获所有异常（除了RecoverableMessageHandlingException），
        // 正常情况下不会抛出异常，而是记录日志
        $this->logger->expects($this->once())
            ->method('info');
            
        $this->logger->expects($this->once())
            ->method('error');

        $this->handler->__invoke($message);
        
        // 验证执行完成，没有异常抛出
        $this->assertTrue(true);
    }

    public function test_invoke_with_empty_command(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('');
        
        $this->logger->expects($this->once())
            ->method('info');
            
        $this->logger->expects($this->once())
            ->method('error');

        $this->handler->__invoke($message);
    }

    public function test_invoke_with_complex_options(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('test:command');
        $message->setOptions([
            '--env' => 'test',
            '--force' => true,
            'argument1' => 'value1',
            '--timeout' => 300
        ]);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('准备执行命令', [
                'command' => 'test:command',
                'options' => [
                    '--env' => 'test',
                    '--force' => true,
                    'argument1' => 'value1',
                    '--timeout' => 300
                ],
            ]);

        $this->logger->expects($this->once())
            ->method('error');

        $this->handler->__invoke($message);
    }

    public function test_constructor_sets_application_properties(): void
    {
        // 使用反射检查内部Application的配置
        $reflection = new \ReflectionClass($this->handler);
        $applicationProperty = $reflection->getProperty('application');
        $applicationProperty->setAccessible(true);
        $application = $applicationProperty->getValue($this->handler);

        $this->assertInstanceOf(Application::class, $application);
    }

    public function test_invoke_without_logger(): void
    {
        $handlerWithoutLogger = new RunCommandHandler($this->kernel);
        $message = new RunCommandMessage();
        $message->setCommand('test:command');

        // 应该没有异常抛出，即使没有logger
        $handlerWithoutLogger->__invoke($message);
        $this->assertTrue(true); // 如果执行到这里说明没有异常
    }

    public function test_invoke_logs_error_with_exception_details(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('non-existent:command');

        $this->logger->expects($this->once())
            ->method('info');

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('异步执行命令时发生异常'),
                $this->callback(function ($context) {
                    return isset($context['command']) &&
                           isset($context['options']) &&
                           isset($context['exception']) &&
                           $context['command'] === 'non-existent:command';
                })
            );

        $this->handler->__invoke($message);
    }
}
