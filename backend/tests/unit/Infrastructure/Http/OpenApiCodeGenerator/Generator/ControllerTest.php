<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Infrastructure\Http\OpenApiCodeGenerator\Generator;

use cebe\openapi\Reader;
use cebe\openapi\spec\Operation;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    private const RESULT = '<?php

declare(strict_types=1);

namespace Core\Endpoints\Http\HelloWorld;

use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    /**
     * @Route("/hello", name="HelloWorld")
     * @param Request $request
     * @return ResponseDTO
     */
    public function __invoke(Request $request): ResponseDTO
    {
        return new Response200();
    }
}
';

    /** @test */
    public function shouldReturnControllerWithRouteAndIdAndRequestAndResponses(): void
    {
        $generator = new Controller();
        /** @var $operation Operation */
        $operation = Reader::readFromYaml(
            <<<'YAML'
                operationId: HelloWorld
                responses:
                  200:
                    description: Found
                YAML,
            Operation::class
        );
        $fileString = $generator->generateFile(
            $operation,
            '/hello',
            new PhpNamespace('Core\Endpoints\Http\HelloWorld')
        );

        self::assertSame(self::RESULT, $fileString);
    }
}
