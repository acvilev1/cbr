<?php

namespace App\Controller;

use App\Common\Validator\ValidatorInterface;
use App\DTO\ExchangeRatesRequestDTO;
use App\Service\ExchangeRatesServiceInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RateController extends AbstractController
{
    private ExchangeRatesServiceInterface $exchangeRatesService;
    private ValidatorInterface $validator;

    public function __construct(
        ExchangeRatesServiceInterface $exchangeRatesService,
        ValidatorInterface $validator
    ) {
        $this->exchangeRatesService = $exchangeRatesService;
        $this->validator = $validator;
    }

    /**
     * Получить все оборудование
     *
     * @Route("/rate", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getRate(Request $request): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        /** @var ExchangeRatesRequestDTO $exchangeRatesRequestDTO */
        $exchangeRatesRequestDTO = $serializer->deserialize(
            json_encode($request->query->all()),
            ExchangeRatesRequestDTO::class,
            'json'
        );
        $this->validator->validate($exchangeRatesRequestDTO);

        $rate = $this->exchangeRatesService->getRate($exchangeRatesRequestDTO);
        $oldRate = $this->exchangeRatesService->getOldRate($exchangeRatesRequestDTO);

        return new JsonResponse(
            [
                'currencyPair' => $this->exchangeRatesService->getCurrencyPair($exchangeRatesRequestDTO),
                'rate' => $rate,
                'diffRate' => $this->exchangeRatesService->diffRate($rate, $oldRate)
            ], Response::HTTP_OK
        );
    }
}
