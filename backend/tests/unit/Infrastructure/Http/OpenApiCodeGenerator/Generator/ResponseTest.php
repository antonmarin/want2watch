<?php

declare(strict_types=1);

namespace Infrastructure\Http\OpenApiCodeGenerator\Generator;

use cebe\openapi\Reader;
use cebe\openapi\spec\Responses;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ResponseTest extends TestCase
{
    private const RESULT = '<?php

declare(strict_types=1);

namespace Core\Endpoints\Http\HelloWorld;

use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;

final class Response200 implements ResponseDTO
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            \'message\' => $this->message,
        ];
    }

    public function getStatus(): int
    {
        return 200;
    }

    public function getHeaders(): array
    {
        return [\'Content-Type\' => \'application/json\'];
    }
}
';
    /** @test */
    public function shouldReturnWhen(): void
    {
        $generator = new Response(new NullLogger());
        /** @var $responses Responses */
        $responses = Reader::readFromYaml(<<<'YAML'
            200:
              description: Found
              content:
                application/json:
                  schema:
                    type: object
                    properties:
                      message:
                        type: string
            YAML, Responses::class);
        $response = $responses->getResponse('200');

        $fileString = $generator->generateFile(
            $response,
            200,
            new PhpNamespace('Core\Endpoints\Http\HelloWorld')
        );

        self::assertSame(self::RESULT, $fileString);
    }
}
