<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel;

use antonmarin\want2watch\tests\support\IterableHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestDTOResolverTest extends TestCase
{
    private TestLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new TestLogger();
    }

    /**
     * @test
     */
    public function shouldSupportWhenDTOImplementRequiredContract(): void
    {
        $resolver = new RequestDTOResolver($this->createMock(ValidatorInterface::class), $this->logger);
        $argument = $this->createMock(ArgumentMetadata::class);
        $argument->method('getType')->willReturn(TestRequestDTO::class);
        self::assertTrue(
            $resolver->supports(
                $this->createMock(Request::class),
                $argument
            )
        );
    }

    /**
     * @test
     */
    public function shouldNotSupportWhenDTONotImplementRequiredContract(): void
    {
        $resolver = new RequestDTOResolver($this->createMock(ValidatorInterface::class), $this->logger);
        $argument = $this->createMock(ArgumentMetadata::class);
        $argument->method('getType')->willReturn(Request::class);
        self::assertFalse(
            $resolver->supports(
                $this->createMock(Request::class),
                $argument
            )
        );
    }

    /**
     * @test
     */
    public function shouldCreateResolvedWhenRequestIsValid(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn([]);
        $resolver = new RequestDTOResolver($validator, $this->logger);

        $request = new Request();
        $meta = new ArgumentMetadata('dto', TestRequestDTO::class, false, false, null);
        $arguments = $resolver->resolve($request, $meta);

        $arguments = IterableHelper::toArray($arguments);
        self::assertCount(1, $arguments);
        $dto = reset($arguments);
        self::assertInstanceOf(TestRequestDTO::class, $dto);
    }

    /**
     * @test
     */
    public function shouldThrowBadRequestExceptionWhenRequestIsNotValid(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $requestErrors = new ConstraintViolationList();
        $requestErrors->add(
            new ConstraintViolation(
                'Error',
                'Error template',
                [],
                null,
                null,
                null,
                null,
                null,
                null
            )
        );
        $validator->method('validate')->willReturn($requestErrors);
        $resolver = new RequestDTOResolver($validator, $this->logger);

        $request = new Request();
        $meta = new ArgumentMetadata('dto', TestRequestDTO::class, false, false, null);
        $this->expectException(BadRequestHttpException::class);
        $resolver->resolve($request, $meta);
    }
}

final class TestRequestDTO implements RequestDTO
{
    public function __construct(Request $request)
    {
    }
}
