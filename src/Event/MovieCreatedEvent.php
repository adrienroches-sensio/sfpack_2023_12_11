<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Movie;
use DateTimeImmutable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class MovieCreatedEvent extends Event
{
    public function __construct(
        public readonly Movie $movie,
        public readonly UserInterface $user,
        public readonly DateTimeImmutable $at,
    ) {
    }
}
