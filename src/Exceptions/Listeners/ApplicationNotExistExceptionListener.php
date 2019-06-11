<?php


namespace App\Exceptions\Listeners;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;


/**
 * Class ApplicationNotExistExceptionListener
 *
 * Listener for the ApplicationNotExistException.
 * This listener is the one that terminates requests coming from unauthorized applications.
 *
 * @package App\Exceptions\Listeners
 * @author @dev1 -> Ore Richard
 */
class ApplicationNotExistExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $message = $event->getException()->getMessage();

        $event->setResponse(new JsonResponse([
            'success' => false,
            'error' => $message
        ], JsonResponse::HTTP_BAD_REQUEST));
    }
}