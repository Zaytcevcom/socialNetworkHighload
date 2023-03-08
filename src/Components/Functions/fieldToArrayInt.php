<?php

declare(strict_types=1);

namespace App\Components\Functions;

/** @param string[] $data */
function fieldToArrayInt(array $data, string $field): array
{
    $arr = [];

    if (isset($data[$field])) {
        $temp = explode(',', trim($data[$field] ?? ''));

        foreach ($temp as $value) {
            $arr[] = (int)$value;
        }
    }

    return array_unique($arr);
}

/** @return int[] */
function toArrayInt(array|string $data): array
{
    $arr = [];

    $temp = \is_array($data) ? $data : explode(',', trim($data));

    /** @var int|string $value */
    foreach ($temp as $value) {
        $arr[] = (int)$value;
    }

    return array_unique($arr);
}
