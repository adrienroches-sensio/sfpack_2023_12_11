<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Model\Movie as MovieModel;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class MovieSlugFormat extends Regex
{
    public function __construct(string $message = null, string $htmlPattern = null, bool $match = null, callable $normalizer = null, array $groups = null, mixed $payload = null, array $options = [])
    {
        parent::__construct('#'.MovieModel::SLUG_FORMAT.'#', $message, $htmlPattern, $match, $normalizer, $groups, $payload, $options);
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}
