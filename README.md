# AsyncCommandBundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![License](https://img.shields.io/packagist/l/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/async-command-bundle/CI/master.svg?style=flat-square)](https://github.com/tourze/async-command-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/async-command-bundle/master.svg?style=flat-square)](https://codecov.io/gh/tourze/async-command-bundle)

A Symfony bundle for executing console commands asynchronously using Symfony Messenger.

## Features

- Execute Symfony console commands asynchronously
- Built-in error handling and logging
- Support for command options and arguments
- Seamless integration with Symfony Messenger

## Dependencies

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Symfony Messenger component
- Monolog for logging

## Installation

Add this bundle to your Symfony project:

```bash
composer require tourze/async-command-bundle
```

## Quick Start

### 1. Enable the bundle

Add the bundle to your `config/bundles.php`:

```php
return [
    // ... other bundles
    Tourze\AsyncCommandBundle\AsyncCommandBundle::class => ['all' => true],
];
```

### 2. Configure Messenger

Configure Symfony Messenger to handle the async command messages:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'doctrine://default'
        routing:
            'Tourze\AsyncCommandBundle\Message\RunCommandMessage': async
```

### 3. Dispatch commands asynchronously

```php
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\AsyncCommandBundle\Message\RunCommandMessage;

class SomeService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function scheduleCommand(): void
    {
        $message = new RunCommandMessage();
        $message->setCommand('app:some-command');
        $message->setOptions([
            '--env' => 'prod',
            '--force' => true,
            'argument1' => 'value1'
        ]);

        $this->messageBus->dispatch($message);
    }
}
```

## Configuration

The bundle works out of the box with minimal configuration. The command handler will:

- Log command execution start and completion
- Handle exceptions gracefully
- Support command retries through Symfony Messenger's retry mechanism

## Advanced Usage

### Using the AsyncCommandService

For a more convenient way to dispatch commands, you can use the `AsyncCommandService`:

```php
use Tourze\AsyncCommandBundle\Service\AsyncCommandService;

class MyService
{
    public function __construct(
        private AsyncCommandService $asyncCommandService
    ) {
    }

    public function scheduleCommand(): void
    {
        $this->asyncCommandService->runCommand('app:some-command', [
            '--env' => 'prod',
            '--force' => true,
            'argument1' => 'value1'
        ]);
    }
}
```

### Error Handling and Retries

Configure retry behavior in your messenger configuration:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: 'doctrine://default'
                retry_strategy:
                    max_retries: 3
                    delay: 1000
                    multiplier: 2
        routing:
            'Tourze\AsyncCommandBundle\Message\RunCommandMessage': async
```

### Logging Configuration

The bundle uses Monolog for logging. You can configure the logger channel:

```yaml
# config/packages/monolog.yaml
monolog:
    channels: ['async_command']
    handlers:
        async_command:
            type: stream
            path: "%kernel.logs_dir%/async_command.log"
            level: info
            channels: ['async_command']
```

## API Reference

### RunCommandMessage

The message class used to dispatch command execution:

```php
class RunCommandMessage implements AsyncMessageInterface
{
    public function getCommand(): string;
    public function setCommand(string $command): void;
    public function getOptions(): array;
    public function setOptions(array $options): void;
}
```

### RunCommandHandler

The message handler that executes commands:

- Decorated with `#[AsMessageHandler]` attribute
- Accepts optional `LoggerInterface` for logging
- Handles both recoverable and non-recoverable exceptions

## Error Handling

The bundle provides robust error handling:

- **Recoverable exceptions**: Re-thrown to allow Messenger retry mechanism
- **Non-recoverable exceptions**: Logged as errors but do not cause message failure
- **Logging**: All command executions are logged with context information

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/async-command-bundle/tests
```

Run PHPStan static analysis:

```bash
./vendor/bin/phpstan analyse packages/async-command-bundle
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

### Development Guidelines

1. Follow PSR-12 coding standards
2. Write unit tests for new features
3. Ensure all tests pass
4. Update documentation as needed

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for details on version history and changes.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.