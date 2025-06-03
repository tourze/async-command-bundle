<?php

namespace Tourze\AsyncCommandBundle\Tests\MessageHandler;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Tourze\AsyncCommandBundle\MessageHandler\RunCommandHandler;

class RunCommandHandlerTest extends TestCase
{
    private $kernel;
    private $logger;
    private $handler;

    protected function setUp(): void
    {
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handler = new RunCommandHandler(
            $this->kernel,
            $this->logger
        );
    }

    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RunCommandHandler::class, $this->handler);
    }
}
