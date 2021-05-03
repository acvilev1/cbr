<?php

namespace App\Service;

use App\DTO\ExchangeRatesRequestDTO;

interface ExchangeRatesServiceInterface
{
    public function getRate(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string;

    public function getOldRate(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string;

    public function getCurrencyPair(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string;

    public function diffRate(string $rate, string $oldRate): string;
}
