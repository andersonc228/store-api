<?php

namespace App\Command;

use App\Shared\Application\UuidGenerator;
use App\User\Domain\Model\User;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fixtures', description: 'Create fixture data')]
class FixturesCommand extends Command
{
    private const string PASSWORD = '123456789';

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, private UuidGenerator $uuidGenerator)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->doctrine->getManager();

        if ($em->getRepository(User::class)->count() === 0) {
            $u1 = new User(
                $this->uuidGenerator->create(),
                'demo@user.com',
                self::PASSWORD,
                new DateTimeImmutable(),
            );
            $em->persist($u1);

            $u2 = new User(
                $this->uuidGenerator->create(),
                'user@demo.com',
                self::PASSWORD,
                new DateTimeImmutable(),
            );
            $em->persist($u2);
            $em->flush();

            $output->writeln('Fixtures users created');
        }

        $users = new Table($output);
        $users->setHeaders(['ID', 'Email', 'Password']);
        array_map(
            static fn(User $user) => $users->addRow([$user->getId(), $user->getEmail(), self::PASSWORD]),
            $em->getRepository(User::class)->findAll(),
        );
        $users->render();

        return Command::SUCCESS;
    }
}

