<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if($exception instanceof HTTPException)
        {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
        } else if (str_contains($exception->getMessage(), "'user.UNIQ_IDENTIFIER_EMAIL'")) {
            $data = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => "This email is already used",
            ];
        } else {
            $data = [
                'status' => 500,
                'message' => $exception->getMessage()
            ];
        }

        $event->setResponse(new JsonResponse($data));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
