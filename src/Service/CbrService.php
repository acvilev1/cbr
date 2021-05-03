<?php

namespace App\Service;

use DateTimeImmutable;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CbrService implements RateServiceInterface
{
    private const API_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    private const EXCLUDE_CURRENCY = 'RUR';

    private HttpClientInterface $httpClient;
    private CurrencyParserInterface $currencyParser;
    private CacheInterface $cache;

    public function __construct(
        HttpClientInterface $httpClient,
        CurrencyParserInterface $currencyParser,
        CacheInterface $cache
    ) {
        $this->httpClient = $httpClient;
        $this->currencyParser = $currencyParser;
        $this->cache = $cache;
    }

    /**
     * @param DateTimeImmutable $dateReq
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    private function getQuotesGivenDay(DateTimeImmutable $dateReq): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            self::API_URL,
            [
                'headers' => [
                    'Accept' => 'application/xml',
                ],
                'query' => [
                    'date_req' => $dateReq->format('d-m-Y'),
                ]
            ]
        );
    }

    /**
     * @param DateTimeImmutable $dateReq
     * @param string            $currencyCode
     *
     * @return float
     * @throws TransportExceptionInterface
     */
    public function getCurrency(DateTimeImmutable $dateReq, string $currencyCode): ?string
    {
        $currencyCode = mb_strtoupper($currencyCode);
        if ($currencyCode === self::EXCLUDE_CURRENCY) {
            return 1;
        }

        return $this->cache->get(
            $this->getCacheKey($dateReq, $currencyCode),
            $this->getCallback($dateReq, $currencyCode)
        );
    }

    /**
     * @param DateTimeImmutable $dateReq
     * @param string            $currencyCode
     *
     * @return callable
     */
    private function getCallback(DateTimeImmutable $dateReq, string $currencyCode): callable
    {
        return function () use ($dateReq, $currencyCode) {
            $xmlCbr = $this->getQuotesGivenDay($dateReq)->getContent();

            return $this->currencyParser->getCurrencyByCode($xmlCbr, $currencyCode);
        };
    }

    /**
     * @param DateTimeImmutable $dateReq
     * @param string            $currencyCode
     *
     * @return string
     */
    private function getCacheKey(DateTimeImmutable $dateReq, string $currencyCode): string
    {
        return $dateReq->format('d-m-Y') . '-' . $currencyCode;
    }
}
