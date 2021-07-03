<?php

declare(strict_types=1);

namespace Infrastructure\Endpoints\Cli;

use Infrastructure\Http\OpenApiCodeGenerator\Specification;
use Infrastructure\Http\OpenApiCodeGenerator\SpecificationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateFromOpenApi extends Command
{
    protected static $defaultName = 'openapi:generate';
    private string $specificationPath;
    private Specification $specification;

    public function __construct(
        string $specificationPath,
        Specification $specification
    ) {
        $this->specificationPath = $specificationPath;
        $this->specification = $specification;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate HTTP entrypoints from OpenAPI specification');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating entrypoints from ' . $this->specificationPath);
        try {
            $this->specification->generateCode($this->specificationPath);

            return self::SUCCESS;
        } catch (SpecificationException $e) {
            $output->writeln('Error reading specification: ' . $e->getMessage());
        }

        return self::FAILURE;
    }
}
