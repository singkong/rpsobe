<?php

namespace App\Enums;

enum AssessmentJenis: string
{
    case Formatif = 'formatif';
    case Sumatif = 'sumatif';

    public function label(): string
    {
        return match ($this) {
            self::Formatif => 'Formatif',
            self::Sumatif => 'Sumatif',
        };
    }
}
