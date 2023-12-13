<?php

declare(strict_types=1);

namespace App\Omdb\Client;

use App\Omdb\Client\Model\Movie;

interface ApiConsumerInterface
{
    public function getByImdbId(string $imdbId): Movie;
}
