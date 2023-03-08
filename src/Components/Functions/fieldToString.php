<?php

declare(strict_types=1);

namespace App\Components\Functions;

/** @param string[] $data */
function fieldToString(?array $data, string $field, string $default = ''): string
{
    $data = fieldToStringOrNull($data, $field);

    if ($data === null) {
        return $default;
    }

    return $data;
}

/** @param string[] $data */
function fieldToStringOrNull(?array $data, string $field): ?string
{
    if (empty($data)) {
        return null;
    }

    if (isset($data[$field])) {
        return trim($data[$field]);
    }

    return null;
}
