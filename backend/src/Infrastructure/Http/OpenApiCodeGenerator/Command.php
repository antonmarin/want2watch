<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Command extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'openapi:generate';
    private string $specificationPath;

    public function __construct(string $specificationPath)
    {
        $this->specificationPath = $specificationPath;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate HTTP entrypoints from OpenAPI specification');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating entrypoints from ' . $this->specificationPath);
        $specification = new Specification(realpath($this->specificationPath));
        $specification->generateCode();

        return self::SUCCESS;
    }

}
