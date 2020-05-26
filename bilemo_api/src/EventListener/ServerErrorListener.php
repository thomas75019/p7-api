<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ServerErrorListener
{
    public function onKernelException(ExceptionEvent $event)
    {

        $excption = $event->getException();
        $response = new Response();

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setContent(json_encode([
            500 => $excption->getMessage()
        ]));


        $event->setResponse($response);

        return $response;
    }
}
