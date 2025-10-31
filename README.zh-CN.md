# AsyncCommandBundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![License](https://img.shields.io/packagist/l/tourze/async-command-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/async-command-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/async-command-bundle/CI/master.svg?style=flat-square)](https://github.com/tourze/async-command-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/async-command-bundle/master.svg?style=flat-square)](https://codecov.io/gh/tourze/async-command-bundle)

一个用于通过 Symfony Messenger 异步执行控制台命令的 Symfony Bundle。

## 特性

- 异步执行 Symfony 控制台命令
- 内置错误处理和日志记录
- 支持命令选项和参数
- 与 Symfony Messenger 无缝集成

## 依赖项

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Symfony Messenger 组件
- Monolog 用于日志记录

## 安装

将此 bundle 添加到您的 Symfony 项目中：

```bash
composer require tourze/async-command-bundle
```

## 快速开始

### 1. 启用 Bundle

在您的 `config/bundles.php` 中添加 bundle：

```php
return [
    // ... 其他 bundles
    Tourze\AsyncCommandBundle\AsyncCommandBundle::class => ['all' => true],
];
```

### 2. 配置 Messenger

配置 Symfony Messenger 来处理异步命令消息：

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

### 3. 异步调度命令

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

## 配置

Bundle 开箱即用，只需最少的配置。命令处理器将：

- 记录命令执行开始和完成
- 优雅地处理异常
- 通过 Symfony Messenger 的重试机制支持命令重试

## 高级用法

### 使用 AsyncCommandService

为了更方便地分派命令，您可以使用 `AsyncCommandService`：

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

### 错误处理和重试

在您的 messenger 配置中配置重试行为：

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

### 日志配置

Bundle 使用 Monolog 进行日志记录。您可以配置日志通道：

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

## API 参考

### RunCommandMessage

用于调度命令执行的消息类：

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

执行命令的消息处理器：

- 使用 `#[AsMessageHandler]` 属性装饰
- 接受可选的 `LoggerInterface` 用于日志记录
- 处理可恢复和不可恢复的异常

## 错误处理

Bundle 提供强大的错误处理：

- **可恢复异常**：重新抛出以允许 Messenger 重试机制
- **不可恢复异常**：记录为错误但不会导致消息失败
- **日志记录**：所有命令执行都会记录上下文信息

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/async-command-bundle/tests
```

运行 PHPStan 静态分析：

```bash
./vendor/bin/phpstan analyse packages/async-command-bundle
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解如何为此项目做出贡献的详细信息。

### 开发指南

1. 遵循 PSR-12 编码标准
2. 为新功能编写单元测试
3. 确保所有测试通过
4. 根据需要更新文档

## 更新日志

请查看 [CHANGELOG.md](CHANGELOG.md) 了解版本历史和更改的详细信息。

## 许可证

MIT 许可证 (MIT)。请查看 [License File](LICENSE) 了解更多信息。
