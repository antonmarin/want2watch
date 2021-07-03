<?php

declare(strict_types=1);

namespace Infrastructure\Http\SymfonyHttpKernel;

use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /** @noinspection MultipleReturnStatementsInspection all returns on same screen*/
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $classString = $argument->getType();
        if ($classString === null || !class_exists($classString)) {
            return false;
        }
        try {
            $reflection = new ReflectionClass($classString);
            if ($reflection->implementsInterface(RequestDTO::class)) {
                return true;
            }
        } catch (ReflectionException $e) {
            $this->logger->notice('Type does not exist', ['type' => $classString, 'exception' => $e]);
        }

        return false;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return object[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        $dto = new $class($request);
        $errors = $this->validator->validate($dto);

        $errorString = '';
        foreach ($errors as $violation) {
            $errorString .= $violation . "\n";
        }
        if ($errorString !== '') {
            throw new BadRequestHttpException($errorString);
        }

        return [$dto];
    }
}
