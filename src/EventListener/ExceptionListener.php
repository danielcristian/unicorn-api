<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if (!$exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(['errors' => ['message' => $exception->getMessage()]]);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $event->setResponse($response);

            return;
        }

        $message['statusCode'] = $exception->getStatusCode();

        if ($exception->getPrevious() instanceof ValidationFailedException) {
            foreach ($exception->getPrevious()->getViolations() as $violation) {
                if ($violation->getPropertyPath()) {
                    $message['errors'][$violation->getPropertyPath()][] = $violation->getMessage();
                }
            }
        } else {
            $message['errors'] = ['message' => $exception->getMessage()];
        }

        // Customize your response object to display the exception details
        $response = new JsonResponse($message);
        $response->setStatusCode($exception->getStatusCode());
        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
