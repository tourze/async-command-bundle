<?php

namespace Tourze\AsyncCommandBundle\Service;

use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;

/**
 * 异步命令服务
 *
 * 提供异步执行命令的统一接口
 */
readonly class AsyncCommandService
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * 异步执行命令
     *
     * @param array<string, mixed> $options
     */
    public function runCommand(string $command, array $options = []): void
    {
        $message = new RunCommandMessage();
        $message->setCommand($command);
        $message->setOptions($options);

        $this->messageBus->dispatch($message);
    }
}
