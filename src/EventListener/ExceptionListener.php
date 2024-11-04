<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class ExceptionListener
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        $content = $this->twig->render('exception/not_found.html.twig', [
            'message' => $exception->getMessage()
        ]);
        $response = new Response();
        $event->setResponse($response->setContent($content));
    }
}


