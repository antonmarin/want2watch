<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\Reader;

final class Specification
{
    private string $specificationPath;

    public function __construct(string $specificationPath)
    {
        $this->specificationPath = $specificationPath;
    }

    /**
     * @throws SpecificationException when any error reading, parsing or validating specification
     */
    public function generateCode(): void
    {
        $realPath = realpath($this->specificationPath);
        if ($realPath === false) {
            throw new SpecificationException('Not existed specification file ' . $this->specificationPath);
        }

        try {
            $openapi = Reader::readFromYamlFile($realPath);
        } catch (IOException $e) {
            throw new SpecificationException(
                'Error reading specification file ' . $this->specificationPath,
                0,
                $e
            );
        } catch (TypeErrorException $e) {
            throw new SpecificationException(
                'Error in specification ' . $this->specificationPath,
                0,
                $e
            );
        } catch (UnresolvableReferenceException $e) {
            throw new SpecificationException(
                'Error resolving reference in specification' . $this->specificationPath,
                0,
                $e
            );
        }

        if ($openapi->validate() === false) {
            $errors = implode(';', $openapi->getErrors());
            throw new SpecificationException(
                'Validation errors in specification' . $this->specificationPath . ':' . $errors
            );
        }
    }
}
