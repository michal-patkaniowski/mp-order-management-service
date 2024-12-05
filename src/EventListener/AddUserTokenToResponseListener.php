<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
/**
 * Summary of AddUserTokenToResponseListener
 *
 * Adds the user token to the response headers.
 * The token is user id.
 */
class AddUserTokenToResponseListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $token = $this->tokenStorage->getToken();
        if ($token) {
            $response->headers->set('Authorization', 'Bearer ' . $token->getUser()->getUserIdentifier());
        }
    }
}
