<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\Schema;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;
use Psr\Log\LoggerInterface;

use function get_class;

final class Response
{
    private Printer $printer;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->printer = new PsrPrinter();
        $this->logger = $logger;
    }

    public function generateFile(
        \cebe\openapi\spec\Response $response,
        int $statusCode,
        PhpNamespace $operationNamespace
    ): string {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace(clone $operationNamespace);
        $namespace->addUse(ResponseDTO::class);

        $class = $namespace->addClass('Response' . $statusCode);
        $class->setFinal()->addImplement(ResponseDTO::class);
        $constructor = $class->addMethod('__construct');
        $serializer = $class->addMethod('jsonSerialize')->setReturnType('array');
        $serializer->addComment('{@inheritdoc}')->addComment('@return array<string,string>');
        $serializer->addBody('return [');
        $contentType = 'application/json';
        /** @var MediaType $mediaType */
        $mediaType = $response->content[$contentType];
        /** @var Schema|null $schema */
        $schema = $mediaType->schema;
        if ($schema instanceof Schema) {
            /** @var Schema $property */
            foreach ($schema->properties as $propertyName => $property) {
                $class->addProperty($propertyName)->setPrivate()->setType($property->type);
                $constructor->addParameter($propertyName)->setType($property->type);
                $constructor->addBody('$this->? = $?;', [$propertyName, $propertyName]);
                $serializer->addBody('    ? => $this->?,', [$propertyName, $propertyName]);
            }
        } else {
            $this->logger->debug('Schema not found');
        }

        $serializer->addBody('];');
        $class->addMethod('getStatus')
            ->setReturnType('int')
            ->addBody('return ?;', [$statusCode]);
        $class->addMethod('getHeaders')
            ->setReturnType('array')
            ->addBody('return [\'Content-Type\' => ?];', [$contentType]);

        return $this->printer->printFile($file);
    }
}
