<?php

declare(strict_types=1);

namespace App\Modules\Post\Console;

use App\Components\Queue\Queue;
use App\Modules\Post\Command\RefreshFeed\RefreshFeedCommand;
use App\Modules\Post\Command\RefreshFeed\RefreshFeedHandler;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function App\Components\env;

final class ConsumerRefreshFeedByUserCommand extends Command
{
    public function __construct(
        private readonly Queue $queue,
        private readonly RefreshFeedHandler $refreshFeedHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:consumer-refresh-feed-by-user')
            ->setDescription('Refresh feed by userId command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callback = function (object $msg) use ($output): void {
            /**
             * @var array{
             *     userId:int
             * } $info
             */
            $info = json_decode((string)$msg->body, true);

            $output->writeln('<info>[UserId - ' . env('APP_ENV') . ']</info> - ' . $info['userId']);

            $this->refreshFeedHandler->handle(new RefreshFeedCommand($info['userId']));
        };

        $this->queue->receive(
            PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_USER),
            $callback
        );

        return 0;
    }
}
