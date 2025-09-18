<?php

declare(strict_types=1);

namespace App\UserWordProgress\Infrastructure\Persistence\Doctrine;

use App\User\Domain\Entity\User;
use App\UserWordProgress\Domain\Entity\UserWordProgress;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWordProgress>
 */
final class UserWordProgressRepository extends ServiceEntityRepository implements UserWordProgressRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, UserWordProgress::class);
    }

    public function save(UserWordProgress $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function findOne(User $user, int $wordId): ?UserWordProgress
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')->setParameter('user', $user)
            ->andWhere('IDENTITY(p.word) = :wordId')->setParameter('wordId', $wordId)
            ->getQuery()->getOneOrNullResult()
        ;
    }

    /**
     * @return list<UserWordProgress>
     */
    public function findDue(User $user, DateTimeImmutable $now, int $limit = 100): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('w')
            ->join('p.word', 'w')
            ->andWhere('p.user = :u')->setParameter('u', $user)
            ->andWhere('p.dueAt IS NOT NULL AND p.dueAt <= :now')->setParameter('now', $now)
            ->orderBy('p.dueAt', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return list<int>
     */
    public function wordIdsByUser(User $user): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('IDENTITY(p.word) AS id')
            ->andWhere('p.user = :u')->setParameter('u', $user)
            ->getQuery()->getScalarResult()
        ;

        return array_map('intval', array_column($rows, 'id'));
    }
}
