<?php

declare(strict_types=1);

namespace App\Components\Validator;

final class Regex
{
    public const FIRST_NAME = '/^[а-яёА-ЯЁa-zA-Z]+$/iu';
    public const SECOND_NAME  = '/^[а-яёА-ЯЁa-zA-Z]+$/iu';
}
