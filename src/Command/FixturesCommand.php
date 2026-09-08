<?php

declare(strict_types=1);

namespace App\Command;

use App\Shared\Application\UuidGenerator;
use App\User\Domain\Model\User;
use App\User\Domain\Model\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fixtures', description: 'Create fixture data')]
class FixturesCommand extends Command
{
    private const string PASSWORD = '123456789';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UuidGenerator $uuidGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->userRepository->count() === 0) {
            $u1 = new User(
                $this->uuidGenerator->create(),
                'demo@user.com',
                self::PASSWORD,
                new DateTimeImmutable(),
            );
            $this->userRepository->save($u1);

            $u2 = new User(
                $this->uuidGenerator->create(),
                'user@demo.com',
                self::PASSWORD,
                new DateTimeImmutable(),
            );
            $this->userRepository->save($u2);

            $output->writeln('Fixtures users created');
        }

        $table = new Table($output);
        $table->setHeaders(['ID', 'Email', 'Password']);

        /** @var User[] $users */
        $users = $this->userRepository->findAll(); // @phpstan-ignore-line

        foreach ($users as $user) {
            $table->addRow([$user->getId(), $user->getEmail(), self::PASSWORD]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
