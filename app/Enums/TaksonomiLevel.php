<?php

namespace App\Enums;

enum TaksonomiLevel: string
{
    case C1 = 'C1';
    case C2 = 'C2';
    case C3 = 'C3';
    case C4 = 'C4';
    case C5 = 'C5';
    case C6 = 'C6';

    public function label(): string
    {
        return match ($this) {
            self::C1 => 'Mengingat',
            self::C2 => 'Memahami',
            self::C3 => 'Menerapkan',
            self::C4 => 'Menganalisis',
            self::C5 => 'Mengevaluasi',
            self::C6 => 'Mencipta',
        };
    }

    public function domain(): string
    {
        return 'kognitif';
    }
}
