<?php

declare(strict_types=1);

namespace App\Modules\Post\Console;

use App\Modules\Friends\Query\GetUserFriendIds\GetUserFriendIdsFetcher;
use App\Modules\Friends\Query\GetUserFriendIds\GetUserFriendIdsQuery;
use App\Modules\Post\Command\RefreshFeed\RefreshFeedCommand;
use App\Modules\Post\Command\RefreshFeed\RefreshFeedHandler;
use App\Modules\Post\Entity\Post\PostRepository;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZayMedia\Shared\Components\Queue\Queue;

use function App\Components\env;

final class ConsumerRefreshFeedByPostCommand extends Command
{
    public function __construct(
        private readonly Queue $queue,
        private readonly PostRepository $postRepository,
        private readonly RefreshFeedHandler $refreshFeedHandler,
        private readonly GetUserFriendIdsFetcher $getUserFriendIdsFetcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:consumer-refresh-feed-by-post')
            ->setDescription('Refresh feed by postId command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callback = function (object $msg) use ($output): void {
            /**
             * @var array{
             *     postId:int
             * } $info
             */
            $info = json_decode((string)$msg->body, true);

            $output->writeln('<info>[PostId - ' . env('APP_ENV') . ']</info> - ' . $info['postId']);

            $post = $this->postRepository->findById($info['postId']);

            if (null === $post) {
                return;
            }

            $this->refreshFeedHandler->handle(new RefreshFeedCommand($post->getUserId()));

            foreach ($this->getFriendIds($post->getUserId()) as $userId) {
                $this->refreshFeedHandler->handle(new RefreshFeedCommand($userId));
            }
        };

        $this->queue->consume(
            PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_POST),
            $callback
        );

        return 0;
    }

    /**
     * @return int[]
     * @throws Exception
     */
    private function getFriendIds(int $userId): array
    {
        $friendIds = $this->getUserFriendIdsFetcher->fetch(
            new GetUserFriendIdsQuery($userId)
        );

        return array_map(fn (int $v) => $v, $friendIds);
    }
}
