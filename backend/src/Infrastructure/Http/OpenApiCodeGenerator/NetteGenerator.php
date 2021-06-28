<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

use antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator\Controller;
use antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator\Request;
use antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator\Response;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Log\LoggerInterface;

final class NetteGenerator
{
    private LoggerInterface $logger;
    private Request $requestGenerator;
    private Response $responseGenerator;
    private Controller $controllerGenerator;
    private string $responseCodesToGenerateRegEx = '/20\d/';
    private string $basePath;
    private string $baseNamespace;

    public function __construct(string $basePath, string $baseNamespace, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->requestGenerator = new Request();
        $this->responseGenerator = new Response($logger);
        $this->controllerGenerator = new Controller();

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

        if ($operation->responses !== null) {
            foreach ($operation->responses as $statusCode => $response) {
                if (preg_match($this->responseCodesToGenerateRegEx, (string)$statusCode) !== 1) {
                    continue;
                }
                file_put_contents(
                    $codePath . "/Response$statusCode.php",
                    $this->responseGenerator->generateFile($response, $statusCode, $operationNamespace)
                );
            }
        }

        $controllerFilename = $codePath . '/Controller.php';
        if (file_exists($controllerFilename)) {
            $this->logger->debug('Controller {file} generation skipped as file exists', ['file' => $controllerFilename]
            );
        } else {
            file_put_contents(
                $controllerFilename,
                $this->controllerGenerator->generateFile($operation, $operationPath, $operationNamespace)
            );
        }
    }
}
