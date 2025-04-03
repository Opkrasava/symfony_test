<?php

namespace App\EventListener;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        // Получаем исключение
        $throwable = $event->getThrowable();

        if ($throwable instanceof ApiException) {
            // Сформируем JSON-ответ
            $responseData = [
                'message'   => $throwable->getMessage(),
                'errors'  => $throwable->getErrors(),
                'code'    => $throwable->getCode(),
            ];

            $response = new JsonResponse($responseData, $throwable->getCode());
            $event->setResponse($response);

            // Если нужно, можно добавить заголовки
            // foreach ($throwable->getHeaders() as $name => $value) {
            //     $response->headers->set($name, $value);
            // }

            return; // Дальше можно вернуть, чтобы не обрабатывать остальные случаи
        }

        // Или, например, какой-то ваш кастомный тип исключения
        // if ($throwable instanceof ValidationException) {
        //     $response = new JsonResponse([...], 400);
        //     $event->setResponse($response);
        //     return;
        // }

        // Если исключение не обрабатывается явно, то дальше пойдёт стандартная страница с ошибкой
    }
}
