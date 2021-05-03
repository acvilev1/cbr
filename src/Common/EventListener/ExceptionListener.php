<?php

declare(strict_types=1);

namespace App\Common\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListener
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var bool */
    private $is_debug;

    /**
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     */
    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;

        $this->is_debug = $kernel->isDebug();
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $response = null;

        $exception = $event->getThrowable();

        switch (get_class($exception)) {
            case BadRequestHttpException::class:
                $response = $this->getResponse(
                    Response::HTTP_BAD_REQUEST,
                    $exception->getMessage()
                );
                break;
            case MethodNotAllowedHttpException::class:
                $response = $this->getResponse(
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    $exception->getMessage()
                );
                break;
            default:
                if ($this->is_debug === false) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                    $response = $this->getResponse(
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        'Внутренняя ошибка сервера'
                    );
                }
        }

        if ($response !== null) {
            $event->setResponse($response);
        }
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return JsonResponse
     */
    private function getResponse(int $code, string $message): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $code,
                'message' => $message
            ],
            $code
        );
    }
}
