<?php

namespace Tourze\AsyncCommandBundle\Tests\Message;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;

/**
 * @internal
 */
#[CoversClass(RunCommandMessage::class)]
final class RunCommandMessageTest extends TestCase
{
    public function testCommandInitialState(): void
    {
        $message = new RunCommandMessage();
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Tourze\AsyncCommandBundle\Message\RunCommandMessage::$command must not be accessed before initialization');

        $command = $message->getCommand();
    }

    public function testOptionsInitialState(): void
    {
        $message = new RunCommandMessage();
        $this->assertEquals([], $message->getOptions());
    }

    public function testCommandGetterAndSetter(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('app:test-command');
        $this->assertEquals('app:test-command', $message->getCommand());
    }

    public function testOptionsGetterAndSetter(): void
    {
        $message = new RunCommandMessage();
        $options = ['force' => true, 'env' => 'test'];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function testCommandWithEmptyString(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('');
        $this->assertEquals('', $message->getCommand());
    }

    public function testCommandWithSpecialCharacters(): void
    {
        $message = new RunCommandMessage();
        $command = 'app:command --option="value with spaces" --flag';
        $message->setCommand($command);
        $this->assertEquals($command, $message->getCommand());
    }

    public function testOptionsWithEmptyArray(): void
    {
        $message = new RunCommandMessage();
        $message->setOptions([]);
        $this->assertEquals([], $message->getOptions());
    }

    public function testOptionsWithComplexArray(): void
    {
        $message = new RunCommandMessage();
        $options = [
            '--env' => 'test',
            '--force' => true,
            '--timeout' => 300,
            'argument1' => 'value1',
            'argument2' => null,
        ];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function testOptionsWithNestedArrays(): void
    {
        $message = new RunCommandMessage();
        $options = [
            '--config' => ['key1' => 'value1', 'key2' => 'value2'],
            '--list' => [1, 2, 3],
        ];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function testCommandImmutabilityAfterMultipleSets(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('first-command');
        $message->setCommand('second-command');
        $this->assertEquals('second-command', $message->getCommand());
    }

    public function testOptionsImmutabilityAfterMultipleSets(): void
    {
        $message = new RunCommandMessage();
        $message->setOptions(['type' => 'first']);
        $message->setOptions(['type' => 'second']);
        $this->assertEquals(['type' => 'second'], $message->getOptions());
    }
}
