<?php

namespace Tourze\AsyncCommandBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use Tourze\AsyncCommandBundle\Service\AsyncCommandService;

/**
 * @internal
 */
#[CoversClass(AsyncCommandService::class)]
final class AsyncCommandServiceTest extends TestCase
{
    private AsyncCommandService $service;

    /** @var array<object> */
    private array $dispatchedMessages = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatchedMessages = [];
        $collector = $this;
        $messageBus = new class($collector) implements MessageBusInterface {
            private AsyncCommandServiceTest $collector;

            public function __construct(AsyncCommandServiceTest $collector)
            {
                $this->collector = $collector;
            }

            public function dispatch(object $message, array $stamps = []): Envelope
            {
                $this->collector->collectMessage($message);

                return new Envelope($message);
            }
        };
        $this->service = new AsyncCommandService($messageBus);
    }

    public function collectMessage(object $message): void
    {
        $this->dispatchedMessages[] = $message;
    }

    public function testRunCommandWithoutOptions(): void
    {
        $command = 'app:test-command';

        $this->service->runCommand($command);

        $this->assertCount(1, $this->dispatchedMessages);
        $message = $this->dispatchedMessages[0];
        $this->assertInstanceOf(RunCommandMessage::class, $message);
        $this->assertEquals($command, $message->getCommand());
        $this->assertEquals([], $message->getOptions());
    }

    public function testRunCommandWithOptions(): void
    {
        $command = 'app:test-command';
        $options = ['--env' => 'test', '--verbose' => true];

        $this->service->runCommand($command, $options);

        $this->assertCount(1, $this->dispatchedMessages);
        $message = $this->dispatchedMessages[0];
        $this->assertInstanceOf(RunCommandMessage::class, $message);
        $this->assertEquals($command, $message->getCommand());
        $this->assertEquals($options, $message->getOptions());
    }
}
