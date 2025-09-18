<?php

declare(strict_types=1);

namespace App\UserAssessment\Domain\Service;

use App\UserAssessment\Application\DTO\QuizDTO;

interface QuizGeneratorInterface
{
    public function generate(): QuizDTO;
}
