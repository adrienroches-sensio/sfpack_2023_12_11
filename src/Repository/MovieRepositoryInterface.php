<?php

declare(strict_types=1);

namespace App\Repository;

interface MovieRepositoryInterface
{
    public function getByIdentifier(string $identifier);
}
