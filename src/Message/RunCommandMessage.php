<?php

namespace Tourze\AsyncCommandBundle\Message;

use Tourze\AsyncContracts\AsyncMessageInterface;

/**
 * 异步运行命令的消息类
 *
 * 该类用于封装需要异步执行的命令及其选项
 */
class RunCommandMessage implements AsyncMessageInterface
{
    /**
     * 要执行的命令
     */
    private string $command;

    /**
     * 获取命令字符串
     *
     * @return string 命令字符串
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * 设置要执行的命令
     *
     * @param string $command 命令字符串
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * 命令执行的选项
     */
    private array $options = [];

    /**
     * 获取命令选项
     *
     * @return array 命令选项数组
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 设置命令选项
     *
     * @param array $options 命令选项数组
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
