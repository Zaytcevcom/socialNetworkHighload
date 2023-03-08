<?php

declare(strict_types=1);

namespace App\Components\Queue;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements Queue
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password
    ) {
        $this->host     = $host;
        $this->port     = $port;
        $this->user     = $user;
        $this->password = $password;

        $this->init();
    }

    public function send(string $queue, array|string $message): void
    {
        if (\is_array($message)) {
            $message = json_encode($message);
        }

        $this->channel->queue_declare(
            queue: $queue,
            auto_delete: false
        );

        $msg = new AMQPMessage($message);

        $this->channel->basic_publish(
            msg: $msg,
            routing_key: $queue
        );
    }

    public function receive(string $queue, callable $callback): void
    {
        while (true) {
            $this->channel->basic_consume(
                queue: $queue,
                no_ack: true,
                callback: $callback
            );

            try {
                $this->channel->wait();
            } catch (Exception) {
                if (!$this->connection->isConnected()) {
                    echo PHP_EOL . '[' . date('Y-m-d H:i:s') . '] Reconnect...' . PHP_EOL;
                    $this->init();
                } else {
                    echo PHP_EOL . '[' . date('Y-m-d H:i:s') . '] Timeout...' . PHP_EOL;
                }
                continue;
            }
        }
    }

    private function init(): void
    {
        $this->connection = new AMQPStreamConnection(
            host: $this->host,
            port: $this->port,
            user: $this->user,
            password: $this->password
        );

        $this->channel = $this->connection->channel();
    }
}
