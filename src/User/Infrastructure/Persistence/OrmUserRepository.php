<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Shared\Common\Functional;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Model\User;
use App\User\Domain\Model\UserRepository;

/** @extends ServiceEntityRepository<User> */
class OrmUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getById(string $id): User
    {
        return $this->find($id);
    }

    /** @throws UserNotFoundException */
    public function findByEmail(string $email): ?User
    {
        if (!$user = $this->findOneBy(['email' => $email])) {
            throw UserNotFoundException::fromEmail($email);
        }

        return $user;
    }

    public function save(User ...$users): void
    {
        $em = $this->getEntityManager();
        Functional::each(static fn (User $user) => $em->persist($user), $users);
        $em->flush();
    }
}
