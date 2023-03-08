<?php

declare(strict_types=1);

namespace App\Modules\Identity\Command\Signup;

use App\Components\Flusher;
use App\Http\Exception\DomainExceptionModule;
use App\Modules\Identity\Entity\User\User;
use App\Modules\Identity\Entity\User\UserRepository;
use App\Modules\Identity\Service\PasswordHasher;
use DateTimeImmutable;

final class IdentitySignupHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $hasher,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(IdentitySignupCommand $command): int
    {
        if ($this->userRepository->findByUsername($command->username)) {
            throw new DomainExceptionModule(
                module: 'identity',
                message: 'error.signup.user_already_exists',
                code: 1
            );
        }

        $user = User::signup(
            username: $command->username,
            firstName: $command->firstName,
            secondName: $command->secondName,
            sex: $command->sex,
            birthdate: new DateTimeImmutable($command->birthdate),
            biography: $command->biography,
            city: $command->city,
            password: $this->hasher->hash($command->password)
        );

        $this->userRepository->add($user);

        $this->flusher->flush();

        return $user->getId();
    }
}
