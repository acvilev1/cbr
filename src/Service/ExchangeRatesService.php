<?php

namespace App\Service;

use App\DTO\ExchangeRatesRequestDTO;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExchangeRatesService implements ExchangeRatesServiceInterface
{
    private const PRECISION = 4;
    private const PRECISION_DIFF = 2;

    private RateServiceInterface $rateService;

    public function __construct(RateServiceInterface $rateService)
    {
        $this->rateService = $rateService;
    }

    /**
     * @param ExchangeRatesRequestDTO $exchangeRatesRequestDTO
     *
     * @return string
     */
    public function getRate(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string
    {
        $currency = $this->getValueCurrency(
            $exchangeRatesRequestDTO->getDateReq(),
            $exchangeRatesRequestDTO->getCurrencyCode()
        );

        $currencyBase = $this->getValueCurrency(
            $exchangeRatesRequestDTO->getDateReq(),
            $exchangeRatesRequestDTO->getBaseCurrencyCode()
        );

        return $this->bcdiv($currencyBase, $currency, self::PRECISION);
    }

    /**
     * @param ExchangeRatesRequestDTO $exchangeRatesRequestDTO
     *
     * @return string
     */
    public function getOldRate(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string
    {
        $oldDate = $exchangeRatesRequestDTO->getDateReq()->modify('-1 day');
        $currency = $this->getValueCurrency($oldDate, $exchangeRatesRequestDTO->getCurrencyCode());
        $currencyBase = $this->getValueCurrency($oldDate, $exchangeRatesRequestDTO->getBaseCurrencyCode());

        return $this->bcdiv($currencyBase, $currency, 4);
    }

    /**
     * @param DateTimeImmutable $date
     * @param string            $currencyCode
     *
     * @return string
     */
    private function getValueCurrency(DateTimeImmutable $date, string $currencyCode): string
    {
        $currencyValue = $this->rateService->getCurrency(
            $date,
            $currencyCode
        );

        if ($currencyValue === null) {
            throw new BadRequestHttpException('Currency ' . $currencyCode . ' not found');
        }

        return $currencyValue;
    }

    /**
     * @param string   $currencyBase
     * @param string   $currency
     * @param int|null $scale
     *
     * @return string|null
     */
    private function bcdiv(string $currencyBase, string $currency, ?int $scale = 0): ?string
    {
        $currencyBase = str_replace(',', '.', $currencyBase);
        $currency = str_replace(',', '.', $currency);

        return bcdiv($currencyBase, $currency, $scale);
    }

    /**
     * @param ExchangeRatesRequestDTO $exchangeRatesRequestDTO
     *
     * @return string
     */
    public function getCurrencyPair(ExchangeRatesRequestDTO $exchangeRatesRequestDTO): string
    {
        return mb_strtoupper($exchangeRatesRequestDTO->getBaseCurrencyCode()) . '/'
            . mb_strtoupper($exchangeRatesRequestDTO->getCurrencyCode());
    }

    /**
     * @param string $rate
     * @param string $oldRate
     *
     * @return string
     */
    public function diffRate(string $rate, string $oldRate): string
    {
        return bcmul(
                bcsub(
                    $this->bcdiv($rate, $oldRate, self::PRECISION),
                    1,
                    self::PRECISION
                ),
                100,
                self::PRECISION_DIFF
            ) . '%';
    }
}

