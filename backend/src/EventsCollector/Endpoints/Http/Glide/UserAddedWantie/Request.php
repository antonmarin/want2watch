<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWantie;

use DateTimeImmutable;
use Exception;
use Infrastructure\Http\SymfonyHttpKernel\RequestDTO;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class Request implements RequestDTO
{
    private string $request;
    private string $idempotencyKey;
    private string $hookKind;
    private DateTimeImmutable $triggeredAt;
    private string $appID;
    private string $wantieTitle;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = (string)$request;
        $this->idempotencyKey = $request->get('idempotencyKey');
        $this->hookKind = $request->get('hookKind');
        try {
            $this->triggeredAt = new DateTimeImmutable($request->get('timestamp'));
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
        $this->appID = $request->get('appID');
        $params = $request->get('params');
        $this->wantieTitle = $params['wantieTitle']['value'];
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function getIdempotencyKey(): string
    {
        return $this->idempotencyKey;
    }

    public function getHookKind(): string
    {
        return $this->hookKind;
    }

    public function getTriggeredAt(): DateTimeImmutable
    {
        return $this->triggeredAt;
    }

    public function getAppID(): string
    {
        return $this->appID;
    }

    public function getWantieTitle(): string
    {
        return $this->wantieTitle;
    }
}
