<?php

declare(strict_types=1);

namespace App\UserAssessment\Infrastructure\Persistence\Doctrine;

use App\UserAssessment\Domain\Entity\UserAssessment;
use App\UserAssessment\Domain\Repository\UserAssessmentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAssessment>
 */
final class UserAssessmentRepository extends ServiceEntityRepository implements UserAssessmentRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, UserAssessment::class);
    }

    public function save(UserAssessment $assessment): void
    {
        $this->getEntityManager()->persist($assessment);
        $this->getEntityManager()->flush();
    }

    public function findLatestByUserId(int $userId): ?UserAssessment
    {
        return $this->findOneBy(['userId' => $userId], ['assessedAt' => 'DESC']);
    }
}
