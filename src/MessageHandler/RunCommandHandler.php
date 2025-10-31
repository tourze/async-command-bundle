<?php

namespace Tourze\AsyncCommandBundle\MessageHandler;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;

/**
 * @see https://symfony.com/doc/current/console/command_in_controller.html
 */
#[AsMessageHandler]
#[WithMonologChannel(channel: 'async_command')]
class RunCommandHandler
{
    private Application $application;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->application = new Application($this->kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
    }

    public function __invoke(RunCommandMessage $message): void
    {
        $this->logger?->info('准备执行命令', [
            'command' => $message->getCommand(),
            'options' => $message->getOptions(),
        ]);

        $input = new ArrayInput([
            'command' => $message->getCommand(),
            ...$message->getOptions(),
        ]);

        $output = new NullOutput();

        try {
            $command = $this->application->get($message->getCommand());
            $command->run($input, $output);
        } catch (RecoverableMessageHandlingException $exception) {
            // 内部有要求重试的话，我们这里直接抛出
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger?->error('异步执行命令时发生异常:' . $exception->getMessage(), [
                'command' => $message->getCommand(),
                'options' => $message->getOptions(),
                'exception' => $exception,
            ]);
        }
    }
}
