<?php

declare(strict_types=1);

namespace App\Word\Infrastructure\Persistence\Doctrine;

use App\Word\Domain\Entity\Word;
use App\Word\Domain\Repository\WordRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Wzorce: Repository, Data Mapper.
 *
 * Implementacja interfejsu `WordRepositoryInterface` z użyciem Doctrine ORM.
 * Odpowiada za bezpośrednią komunikację z bazą danych dla encji `Word`.
 *
 * ### Wzorce:
 * - **Repository** — udostępnia metody do pracy z encjami `Word` bez ujawniania szczegółów infrastruktury.
 * - **Data Mapper** — korzystając z Doctrine, oddziela encje od logiki zapisu/odczytu z bazy.
 *
 * ### Odpowiedzialność:
 * - Wyszukiwanie (`search()`), paginacja (`slice()`), agregacja (`totalCount()`).
 * - Zapisywanie i usuwanie encji (`add()`, `remove()`).
 * - Wyszukiwanie po ID (`byId()`).
 *
 * ### Uwaga:
 * Korzysta z `ServiceEntityRepository` — czyli repozytorium zintegrowanego z Doctrine.
 * Encje nie są zależne od tego repozytorium — to zgodne z zasadą Separation of Concerns.
 */
/**
 * @extends ServiceEntityRepository<Word>
 */
final class WordDoctrineRepository extends ServiceEntityRepository implements WordRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Word::class);
    }

    /** @return array{items: list<Word>, total: int} */
    public function search(?string $q, ?string $level, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('w');

        if ($q) {
            $qb->andWhere('LOWER(w.headword) LIKE :q OR LOWER(w.translation) LIKE :q')
                ->setParameter('q', '%'.mb_strtolower($q).'%')
            ;
        }
        if ($level) {
            $qb->andWhere('w.level = :level')->setParameter('level', $level);
        }

        $qb->orderBy('w.id', 'DESC');

        $total = (int) (clone $qb)->select('COUNT(w.id)')->getQuery()->getSingleScalarResult();

        $items = $qb->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()->getResult()
        ;

        /** @var list<Word> $items */
        $items = array_values($items);

        return ['items' => $items, 'total' => $total];
    }

    public function add(Word $word): void
    {
        $em = $this->getEntityManager();
        $em->persist($word);
        $em->flush();
    }

    public function remove(Word $word): void
    {
        $em = $this->getEntityManager();
        $em->remove($word);
        $em->flush();
    }

    public function byId(int $id): ?Word
    {
        return $this->find($id);
    }

    public function totalCount(): int
    {
        return (int) $this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /** @return list<Word> */
    public function slice(int $offset, int $limit): array
    {
        $rows = $this->createQueryBuilder('w')
            ->orderBy('w.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()->getResult()
        ;

        return array_values($rows);
    }
}
