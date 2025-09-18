<?php

declare(strict_types=1);

namespace App\UserWordProgress\Domain\Service;

use App\UserWordProgress\Domain\Entity\UserWordProgress;
use App\UserWordProgress\Domain\Enum\ReviewStatus;
use DateTimeImmutable;

final class SrsService
{
    public function update(UserWordProgress $uw, int $q, DateTimeImmutable $today): void
    {
        $ef = max(1.3, $uw->getEf() + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02)));

        if ($q < 3) {
            $uw->setReps(0);
            $uw->setInterval(1);
            $uw->setDueAt($today->modify('+1 day'));
        } else {
            $reps = $uw->getReps() + 1;
            $uw->setReps($reps);
            $interval = match (true) {
                1 === $reps => 1,
                2 === $reps => 6,
                default => (int) round(max(1, $uw->getInterval()) * $ef),
            };
            $uw->setInterval($interval);
            $uw->setDueAt($today->modify("+{$interval} days"));
        }

        $uw->setEf($ef);
        $uw->setLastResult($q);
        if ($uw->getInterval() >= 30 && $q >= 4) {
            $uw->setStatus(ReviewStatus::LEARNED);
        }
    }
}
