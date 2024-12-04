<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Summary of AddUserTokenToResponseListener
 *
 * Adds the user token to the response headers.
 * The token is stored in the request attributes by the UserTokenAuthenticator.
 */
class AddUserTokenToResponseListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->attributes->has('auth_token')) {
            $authToken = $request->attributes->get('auth_token');
            $response->headers->set('Authorization', 'Bearer ' . $authToken);
        }
    }
}
