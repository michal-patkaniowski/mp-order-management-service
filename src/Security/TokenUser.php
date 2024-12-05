<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenUser implements UserInterface, UserProviderInterface
{
    private string $token;

    public function __construct(string $token = '')
    {
        $this->token = $token;
    }

    public function getUserIdentifier(): string
    {
        return $this->token;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function supportsClass(string $class): bool
    {
        return $class === TokenUser::class || is_subclass_of($class, TokenUser::class);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof self) {
            throw new \LogicException('Invalid user class.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function loadUserByUsername(string $username)
    {
        return new self($username);
    }
}
