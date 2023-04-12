<?php

declare(strict_types=1);

namespace App\Console;

use App\Modules\Identity\Entity\User\User;
use App\Modules\Identity\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('generate:users')
            ->setDescription('Generate 1 000 000 users command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $locale = 'ru_RU';
        $password = (new PasswordHasher())->hash('1234567890');

        for ($i = 0; $i <= 1_000_000; ++$i) {
            $user = User::signup(
                username: Factory::create($locale)->userName() . $i,
                firstName: Factory::create($locale)->firstName,
                secondName: Factory::create($locale)->lastName,
                sex: rand(0, 1),
                birthdate: new DateTimeImmutable('1996-03-18'),
                biography: Factory::create($locale)->text,
                city: Factory::create($locale)->city,
                password: $password
            );

            $this->em->persist($user);

            if ($i % 10000 === 0) {
                $output->writeln('<info>Generated: ' . $i . '</info>');
                $this->em->flush();
                $this->em->clear();
            }
        }

        $output->writeln('<info>Done!</info>');

        return 0;
    }
}
