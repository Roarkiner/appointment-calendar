<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessSubscriber
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user->isStatus()) {
            throw new CustomUserMessageAuthenticationException('Your account is deactivated.');
        }
    }
}