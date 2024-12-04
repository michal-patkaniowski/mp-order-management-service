<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Summary of UserTokenAuthenticator
 *
 * Authenticates requests using a temporary token.
 * If the request contains a valid token, it is used for authentication.
 * If the request does not contain a valid token, a new token is generated.
 * The token is stored in the request attributes and used to authenticate/identify the user.
 *
 * See also: AddUserTokenToResponseListener
 */
class UserTokenAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');

        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
            if (!$this->isValidToken($token)) {
                $token = $this->generateToken();
            }
        } else {
            $token = $this->generateToken();
        }

        $request->attributes->set('auth_token', $token);

        return new SelfValidatingPassport(new UserBadge('authenticated', function (): TokenUser {
            return new TokenUser();
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Authentication Failed',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function isValidToken(string $token): bool
    {
        return strlen($token) === 32;
    }
}
