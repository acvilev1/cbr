<?php

namespace App\Service;

use DateTimeImmutable;

interface RateServiceInterface
{
    public function getCurrency(DateTimeImmutable $dateReq, string $currencyCode): ?string;
}
