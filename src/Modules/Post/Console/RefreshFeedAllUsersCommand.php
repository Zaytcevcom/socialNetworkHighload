<?php

declare(strict_types=1);

namespace App\Modules\Post\Console;

use App\Components\Queue\Queue;
use App\Modules\Identity\Entity\User\User;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RefreshFeedAllUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Queue $queue,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:refresh-feed-all-users')
            ->setDescription('Refresh feed for all users command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 0;
        $count = 100;
        $max_id = null;

        $output->writeln('<info>Start refresh feed for all users</info>');

        while (true) {
            $criteria = Criteria::create();

            if (null !== $max_id) {
                $criteria->andWhere(Criteria::expr()->lt('id', $max_id));
            }

            /** @var User[] $users */
            $users = $this->em->getRepository(User::class)
                ->matching(
                    $criteria
                        ->orderBy(['id' => 'DESC'])
                        ->setMaxResults($count)
                );

            if (\count($users) === 0) {
                break;
            }

            foreach ($users as $user) {
                $max_id = $user->getId();

                $this->sendToQueueRefreshFeedByUser($user->getId());
            }

            ++$i;

            $output->writeln('Count: ' . ($i * $count));
        }

        $output->writeln('<info>Done!</info>');

        return 0;
    }

    private function sendToQueueRefreshFeedByUser(int $userId): void
    {
        $this->queue->send(
            queue: PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_USER),
            message: ['userId' => $userId]
        );
    }
}
