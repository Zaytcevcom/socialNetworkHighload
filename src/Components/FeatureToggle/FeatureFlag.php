<?php

declare(strict_types=1);

namespace App\Components\FeatureToggle;

interface FeatureFlag
{
    public function isEnabled(string $name): bool;
}
