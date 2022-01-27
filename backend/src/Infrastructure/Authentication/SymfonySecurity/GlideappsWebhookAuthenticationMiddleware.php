<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\SymfonySecurity;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * Authentication for glideapps webhooks
 *
 * @link config/packages/security.yaml
 * @noinspection PhpUnused
 */
final class GlideappsWebhookAuthenticationMiddleware extends AbstractAuthenticator implements AuthenticatorInterface
{
    private const AUTHORIZATION_HEADER = 'Authorization';
    private const USER_IDENTIFIER = 'glide';

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::AUTHORIZATION_HEADER);
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get(self::AUTHORIZATION_HEADER);
        if (null === $header) {
            throw new CustomUserMessageAuthenticationException('No authorization provided');
        }
        $password = str_replace('Basic ', '', $header);

        return new Passport(new UserBadge(self::USER_IDENTIFIER), new PasswordCredentials($password));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
