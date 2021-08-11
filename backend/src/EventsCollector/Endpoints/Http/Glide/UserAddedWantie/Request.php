<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWantie;

use Infrastructure\Http\SymfonyHttpKernel\RequestDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class Request implements RequestDTO
{
    private string $request;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = (string) $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }
}
