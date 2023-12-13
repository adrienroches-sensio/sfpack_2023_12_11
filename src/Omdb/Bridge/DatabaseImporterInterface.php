<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Entity\Movie as MovieEntity;
use App\Omdb\Client\Model\Movie as MovieOmdb;

interface DatabaseImporterInterface
{
    public function import(MovieOmdb $movieOmdb, bool $flush = false): MovieEntity;
}
