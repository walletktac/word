<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\DTO;

/**
 * @phpstan-type AnswerList array<string, string>
 */
final readonly class QuizAnswerDTO
{
    public function __construct(
        public int $userId,
        /** @var AnswerList */
        public array $answers
    ) {}
}
