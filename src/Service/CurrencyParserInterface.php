<?php

namespace App\Service;

interface CurrencyParserInterface
{
    public function getCurrencyByCode(string $data, string $currencyCode): ?string;
}
