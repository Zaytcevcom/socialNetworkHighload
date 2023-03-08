<?php

declare(strict_types=1);

namespace App\Components\Functions;

/** @param string[] $data */
function fieldToArrayString(array $data, string $field): array
{
    $arr = [];

    if (isset($data[$field])) {
        $temp = explode(',', trim($data[$field] ?? ''));

        foreach ($temp as $value) {
            $arr[] = $value;
        }
    }

    return array_unique($arr);
}

/** @return string[] */
function toArrayString(array|string $data): array
{
    $arr = [];

    $temp = \is_array($data) ? $data : explode(',', trim($data));

    /** @var string $value */
    foreach ($temp as $value) {
        $arr[] = $value;
    }

    return array_unique($arr);
}

function toSplitValuesString(array|string $data): string
{
    return implode(',', toArrayString($data));
}
