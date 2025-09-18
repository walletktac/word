<?php

declare(strict_types=1);

namespace App\UserWordProgress\Infrastructure\Http;

use App\User\Domain\Entity\User;
use App\UserWordProgress\Application\Command\AddWordToStudyCommand;
use App\UserWordProgress\Application\Command\ReviewWordCommand;
use App\UserWordProgress\Application\Handler\AddWordToStudyHandler;
use App\UserWordProgress\Application\Handler\ReviewWordHandler;
use App\UserWordProgress\Application\Service\DailyPlanService;
use App\UserWordProgress\Domain\Entity\UserWordProgress;
use App\UserWordProgress\Domain\Enum\ReviewStatus;
use App\UserWordProgress\Domain\Repository\UserWordProgressRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user_word_progress')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class UserWordProgressController extends AbstractController
{
    public function __construct(
        private DailyPlanService $plan,
        private UserWordProgressRepositoryInterface $progressRepo,
        private AddWordToStudyHandler $addHandler,
        private ReviewWordHandler $reviewHandler,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/daily', methods: ['GET'])]
    public function daily(Request $req): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $target = $req->query->getInt('target', 10);
        $reviewFirstDays = $req->query->getInt('reviewFirstDays', 2);
        $reviewFirstMin = $req->query->getInt('reviewFirstMin', 5);

        $dateParam = $req->query->get('date');
        $date = new DateTimeImmutable(is_string($dateParam) ? $dateParam : 'now');

        $res = $this->plan->buildWithReviewFirst($user, $date, $target, $reviewFirstDays, $reviewFirstMin);

        return $this->json($res);
    }

    #[Route('/due', methods: ['GET'])]
    public function due(Request $req): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $limit = $req->query->getInt('limit', 100);
        $now = new DateTimeImmutable();

        $rows = $this->progressRepo->findDue($user, $now, $limit);

        $out = array_map(static function (UserWordProgress $userWordProgress) {
            $word = $userWordProgress->getWord();
            $wordId = $word->getId();
            if (null === $wordId) {
                throw new LogicException('Word ID is null in due list.');
            }

            return [
                'wordId' => $wordId,
                'headword' => $word->getHeadword(),
                'dueAt' => $userWordProgress->getDueAt()?->format(DATE_ATOM),
                'ef' => $userWordProgress->getEf(),
                'interval' => $userWordProgress->getInterval(),
                'reps' => $userWordProgress->getReps(),
                'status' => $userWordProgress->getStatus()->value,
            ];
        }, $rows);

        return $this->json(['items' => $out]);
    }

    #[Route('/{wordId}/add', methods: ['POST'])]
    public function add(int $wordId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();
        if (null === $userId) {
            throw new LogicException('Authenticated user has null ID.');
        }

        ($this->addHandler)(new AddWordToStudyCommand($userId, $wordId));

        return $this->json(['ok' => true]);
    }

    #[Route('/{wordId}/review', methods: ['POST'])]
    public function review(int $wordId, Request $req): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();
        if (null === $userId) {
            throw new LogicException('Authenticated user has null ID.');
        }

        $p = json_decode($req->getContent(), true) ?? [];
        $grade = (int) ($p['grade'] ?? 0);

        ($this->reviewHandler)(new ReviewWordCommand($userId, $wordId, $grade));

        return $this->json(['ok' => true]);
    }

    #[Route('/{wordId}/mark-known', methods: ['POST'])]
    public function markKnown(int $wordId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $progress = $this->ensureProgress($user, $wordId);

        $progress->setStatus(ReviewStatus::LEARNED);
        $progress->setInterval(9999);
        $progress->setDueAt(null);
        $this->entityManager->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{wordId}/mark-to-review', methods: ['POST'])]
    public function markToReview(int $wordId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $progress = $this->ensureProgress($user, $wordId);

        $progress->setStatus(ReviewStatus::LEARNING);
        $progress->setDueAt(new DateTimeImmutable());
        $progress->setInterval(1);
        $this->entityManager->flush();

        return $this->json(['ok' => true]);
    }

    /** Gwarantuje, że wpis istnieje; tworzy go w razie potrzeby. */
    private function ensureProgress(User $user, int $wordId): UserWordProgress
    {
        $existing = $this->progressRepo->findOne($user, $wordId);
        if ($existing) {
            return $existing;
        }

        $userId = $user->getId();
        if (null === $userId) {
            throw new LogicException('Authenticated user has null ID.');
        }

        ($this->addHandler)(new AddWordToStudyCommand($userId, $wordId));
        $created = $this->progressRepo->findOne($user, $wordId);
        if (!$created) {
            throw $this->createNotFoundException('Word not found');
        }

        return $created;
    }
}
