<?php

namespace App\Enums;

enum Jenjang: string
{
    case D3 = 'D3';
    case D4 = 'D4';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';
    case Profesi = 'Profesi';
    case Spesialis = 'Spesialis';

    public function label(): string
    {
        return $this->value;
    }
}
