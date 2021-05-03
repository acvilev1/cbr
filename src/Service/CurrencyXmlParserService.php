<?php

namespace App\Service;

use Exception;
use SimpleXMLElement;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class CurrencyXmlParserService implements CurrencyParserInterface
{
    private DecoderInterface $decoder;

    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    /**
     * @param string $data
     * @param string $currencyCode
     *
     * @return string|null
     * @throws Exception
     */
    public function getCurrencyByCode(string $data, string $currencyCode): ?string
    {
        $xml = new SimpleXMLElement($data);

        $results = $xml->xpath('/ValCurs/Valute/CharCode[text()="' . $currencyCode . '"]/parent::*/Value');

        foreach ($results as $currency) {
            return (string) ($currency);
        }

        return null;
    }
}
