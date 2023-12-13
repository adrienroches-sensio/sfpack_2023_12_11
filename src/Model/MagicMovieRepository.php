<?php

declare(strict_types=1);

namespace App\Model;

use App\Repository\MovieRepositoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class MagicMovieRepository
{
    /**
     * @param ServiceLocator<MovieRepositoryInterface> $container
     */
    public function __construct(
        #[TaggedLocator('movie.repository', indexAttribute: 'key')]
        private readonly ServiceLocator $container,
    ) {
    }

    public function get(string $type, string $identifier)
    {
        return $this->container->get($type)->getByIdentifier($identifier);
    }
}
