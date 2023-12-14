<?php

namespace App\EventSubscriber;

use App\Event\MovieCreatedEvent;
use App\Model\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MovieSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function notifyAllAdminsIfNotGeneralAudience(MovieCreatedEvent $event): void
    {
        if ($event->movie->getRated() === Rating::GeneralAudiences) {
            return;
        }

        dump('TODO : Must fetch and notify all admins that a new movie must be reviewed.');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MovieCreatedEvent::class => [
                ['notifyAllAdminsIfNotGeneralAudience', 0]
            ],
        ];
    }
}
