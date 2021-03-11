<?php

declare(strict_types=1);

namespace antonmarin\want2watch\tests\support;

final class IterableHelper
{
    /**
     * @param iterable $iterable
     * @return mixed
     */
    public static function first(iterable $iterable)
    {
        $dto = null;
        foreach ($iterable as $argument) {
            $dto = $argument;
        }

        return $dto;
    }

    public static function toArray(iterable $iterable): array
    {
        $array = [];
        foreach ($iterable as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
