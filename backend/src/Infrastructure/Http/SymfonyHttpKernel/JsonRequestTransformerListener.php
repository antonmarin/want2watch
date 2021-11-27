<?php

declare(strict_types=1);

namespace Infrastructure\Http\SymfonyHttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class JsonRequestTransformerListener
{
    private const JSON = 'json';

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($this->isSupported($request) === false) {
            return;
        }

        $request->request->replace($request->toArray());
    }

    private function isSupported(Request $request): bool
    {
        $requestBody = $request->getContent();
        return $request->getContentType() === self::JSON
            && $requestBody !== '';
    }
}
