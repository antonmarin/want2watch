<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

use antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator\Request;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Log\LoggerInterface;

final class NetteGenerator
{
    private LoggerInterface $logger;
    private Request $requestGenerator;
    private string $basePath;
    private string $baseNamespace;

    public function __construct(string $basePath, string $baseNamespace, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->requestGenerator = new Request();
        // todo redo?
        // this should be dynamically as spec may declare multiple contexts
        // or every context should have separated specification?
        $this->basePath = $basePath;
        $this->baseNamespace = $baseNamespace;
    }

    public function generate(OpenApi $openapi): void
    {
        /** @var PathItem $pathItem */
        foreach ($openapi->paths as $path => $pathItem) {
            $this->logger->debug('Processing {path} path', ['path' => $path]);
            // todo validate operationId is unique here
            foreach ($pathItem->getOperations() as $method => $operation) {
                $operationNamespace = new PhpNamespace($this->baseNamespace . '\\' . $operation->operationId);
                $this->generateOperationCode($operation, $operationNamespace, $method, $path);
            }
        }
    }

    private function generateOperationCode(
        Operation $operation,
        PhpNamespace $operationNamespace,
        string $method,
        string $operationPath
    ): void {
        $codePath = $this->basePath . '/' . $operation->operationId;
        $this->logger->debug(
            'Generating code for operation {id} ({method} {operationPath}) in {ns} namespace in {codePath}',
            [
                'id' => $operation->operationId,
                'method' => strtoupper($method),
                'operationPath' => $operationPath,
                'ns' => $operationNamespace->getName(),
                'codePath' => $codePath,
            ]
        );
        file_put_contents(
            $codePath . '/Request.php',
            $this->requestGenerator->generateFile($operation, $operationNamespace)
        );
    }
}
