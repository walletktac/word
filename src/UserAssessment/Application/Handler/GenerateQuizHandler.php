<?php

declare(strict_types=1);

namespace App\UserAssessment\Application\Handler;

use App\UserAssessment\Application\DTO\QuizDTO;
use App\UserAssessment\Domain\Service\QuizGeneratorInterface;

final readonly class GenerateQuizHandler
{
    public function __construct(private QuizGeneratorInterface $generator) {}

    public function __invoke(): QuizDTO
    {
        return $this->generator->generate();
    }
}
