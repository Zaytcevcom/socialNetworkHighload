<?php

declare(strict_types=1);

namespace App\Components\Queue;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements Queue
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;

    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

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

        if (null === $this->channel) {
            return;
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
            if (null === $this->channel || null === $this->connection) {
                echo PHP_EOL . '[' . date('Y-m-d H:i:s') . '] Reconnect [1]...' . PHP_EOL;

                echo PHP_EOL . 'Sleep 5 sec...' . PHP_EOL;
                sleep(5);

                $this->init();
                continue;
            }

            if (!$this->connection->isConnected()) {
                echo PHP_EOL . '[' . date('Y-m-d H:i:s') . '] Reconnect [2]...' . PHP_EOL;

                echo PHP_EOL . 'Sleep 5 sec...' . PHP_EOL;
                sleep(5);

                $this->resetConnection();
                continue;
            }

            try {
                $this->channel->basic_consume(
                    queue: $queue,
                    no_ack: true,
                    callback: $callback
                );
            } catch (AMQPTimeoutException) {
                continue;
            } catch (Exception $e) {
                echo PHP_EOL . $e->getMessage() . PHP_EOL;

                $this->resetConnection();

                echo PHP_EOL . 'Sleep 30 sec...' . PHP_EOL;
                sleep(30);

                continue;
            }

            try {
                $this->channel->wait();
            } catch (Exception) {
            }
        }
    }

    private function init(): void
    {
        try {
            $this->connection = new AMQPStreamConnection(
                host: $this->host,
                port: $this->port,
                user: $this->user,
                password: $this->password
            );

            $this->channel = $this->connection->channel();
        } catch (Exception) {
        }
    }

    private function resetConnection(): void
    {
        $this->channel = null;
        $this->connection = null;
    }
}
