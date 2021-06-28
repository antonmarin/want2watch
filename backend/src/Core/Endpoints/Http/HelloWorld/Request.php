<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use Symfony\Component\Validator\Constraints as Assert;
use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\RequestDTO;

final class Request implements RequestDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private string $name;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->name = (string) $request->query->get('name');
    }

    public function getName(): string
    {
        return $this->name;
    }
}
