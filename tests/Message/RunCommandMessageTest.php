<?php

namespace Tourze\AsyncCommandBundle\Tests\Message;

use PHPUnit\Framework\TestCase;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;
use Tourze\AsyncContracts\AsyncMessageInterface;

class RunCommandMessageTest extends TestCase
{
    public function test_implements_async_message_interface(): void
    {
        $message = new RunCommandMessage();
        $this->assertInstanceOf(AsyncMessageInterface::class, $message);
    }

    public function test_command_initial_state(): void
    {
        $message = new RunCommandMessage();
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Tourze\AsyncCommandBundle\Message\RunCommandMessage::$command must not be accessed before initialization');
        
        $command = $message->getCommand();
    }

    public function test_options_initial_state(): void
    {
        $message = new RunCommandMessage();
        $this->assertEquals([], $message->getOptions());
    }

    public function test_command_getter_and_setter(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('app:test-command');
        $this->assertEquals('app:test-command', $message->getCommand());
    }

    public function test_options_getter_and_setter(): void
    {
        $message = new RunCommandMessage();
        $options = ['--force', '--env=test'];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function test_command_with_empty_string(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('');
        $this->assertEquals('', $message->getCommand());
    }

    public function test_command_with_special_characters(): void
    {
        $message = new RunCommandMessage();
        $command = 'app:command --option="value with spaces" --flag';
        $message->setCommand($command);
        $this->assertEquals($command, $message->getCommand());
    }

    public function test_options_with_empty_array(): void
    {
        $message = new RunCommandMessage();
        $message->setOptions([]);
        $this->assertEquals([], $message->getOptions());
    }

    public function test_options_with_complex_array(): void
    {
        $message = new RunCommandMessage();
        $options = [
            '--env' => 'test',
            '--force' => true,
            '--timeout' => 300,
            'argument1' => 'value1',
            'argument2' => null
        ];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function test_options_with_nested_arrays(): void
    {
        $message = new RunCommandMessage();
        $options = [
            '--config' => ['key1' => 'value1', 'key2' => 'value2'],
            '--list' => [1, 2, 3]
        ];
        $message->setOptions($options);
        $this->assertEquals($options, $message->getOptions());
    }

    public function test_command_immutability_after_multiple_sets(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('first-command');
        $message->setCommand('second-command');
        $this->assertEquals('second-command', $message->getCommand());
    }

    public function test_options_immutability_after_multiple_sets(): void
    {
        $message = new RunCommandMessage();
        $message->setOptions(['--first']);
        $message->setOptions(['--second']);
        $this->assertEquals(['--second'], $message->getOptions());
    }
}
