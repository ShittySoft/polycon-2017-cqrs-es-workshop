<?php

declare(strict_types=1);

namespace Building\Domain\Restriction;

interface AllowedToCheckIn
{
    public function __invoke(string $username) : bool;
}
