<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\RequestDTO;
use cebe\openapi\Reader;
use cebe\openapi\spec\Operation;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private const REQUIRED_STRING_IN_QUERY = '<?php

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
        $this->name = (string) $request->query->get(\'name\');
    }

    public function getName(): string
    {
        return $this->name;
    }
}
';

    /** @test */
    public function should_return_valid_file_string(): void
    {
        $generator = new Request();
        /** @var $operation Operation */
        $operation = Reader::readFromYaml(
            <<<'YAML'
operationId: HelloWorld
parameters:
- name: name
  in: query
  required: true
  schema:
    type: string
YAML
            ,
            Operation::class
        );

        $fileString = $generator->generateFile(
            $operation,
            new PhpNamespace('antonmarin\want2watch\Core\Endpoints\Http\HelloWorld')
        );

        self::assertEquals(self::REQUIRED_STRING_IN_QUERY, $fileString);
    }
}
