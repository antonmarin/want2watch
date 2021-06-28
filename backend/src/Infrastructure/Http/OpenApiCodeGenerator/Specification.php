<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\Reader;

final class Specification
{
    private NetteGenerator $generator;

    public function __construct(NetteGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @throws SpecificationException when any error reading, parsing or validating specification
     */
    public function generateCode(string $specificationPath): void
    {
        $realPath = realpath($specificationPath);
        if ($realPath === false) {
            throw new SpecificationException('Not existed specification file ' . $specificationPath);
        }

        try {
            $openapi = Reader::readFromYamlFile($realPath);
        } catch (IOException $e) {
            throw new SpecificationException(
                'Error reading specification file ' . $specificationPath,
                0,
                $e
            );
        } catch (TypeErrorException $e) {
            throw new SpecificationException(
                'Error in specification ' . $specificationPath,
                0,
                $e
            );
        } catch (UnresolvableReferenceException $e) {
            throw new SpecificationException(
                'Error resolving reference in specification' . $specificationPath,
                0,
                $e
            );
        }

        try {
            $openapi->resolveReferences();
        } catch (UnresolvableReferenceException $e) {
            throw new SpecificationException('Specification has unresolvable references', 0, $e);
        }

        if ($openapi->validate() === false) {
            $errors = implode(';', $openapi->getErrors());
            throw new SpecificationException(
                'Validation errors in specification' . $specificationPath . ':' . $errors
            );
        }

        $this->generator->generate($openapi);
    }
}
