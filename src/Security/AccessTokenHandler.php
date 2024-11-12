<?php
// src/Security/AccessTokenHandler.php
namespace App\Security;

use App\Repository\AccessTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use App\Repository\UserRepository;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $user = $this->userRepository->findOneBy(['token' => $accessToken]);

        if (!$user) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($user->getUserIdentifier());
    }
}