<?php

declare(strict_types=1);

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class ExchangeRatesRequestDTO
{
    /**
     * @var DateTimeImmutable
     * @Assert\NotBlank(
     *     message="Дата обязательный параметр"
     * )
     * @Serializer\Type(name="DateTimeImmutable<'d-m-Y'>")
     */
    private DateTimeImmutable $dateReq;

    /**
     * @var string
     * @Assert\Length(
     *     max=3,
     *     min=3,
     *     maxMessage="Код валюты состоит из 3 символов",
     *     minMessage="Код валюты состоит из 3 символов"
     * )
     * @Assert\NotBlank(
     *     message="Код валюты обязательно для заполнения"
     * )
     * @Serializer\Type(name="string")
     */
    private string $currencyCode;

    /**
     * @var string
     * @Assert\Length(
     *     max=3,
     *     min=3,
     *     maxMessage="Код валюты состоит из 3 символов",
     *     minMessage="Код валюты состоит из 3 символов"
     * )
     * @Serializer\Type(name="string")
     */
    private string $baseCurrencyCode = 'RUR';

    /**
     * @return DateTimeImmutable
     */
    public function getDateReq(): DateTimeImmutable
    {
        return $this->dateReq;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrencyCode;
    }
}
