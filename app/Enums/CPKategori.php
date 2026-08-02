<?php

namespace App\Enums;

enum CPKategori: string
{
    case Sikap = 'S';
    case Pengetahuan = 'P';
    case KeterampilanUmum = 'KU';
    case KeterampilanKhusus = 'KK';

    public function label(): string
    {
        return match ($this) {
            self::Sikap => 'Sikap',
            self::Pengetahuan => 'Pengetahuan',
            self::KeterampilanUmum => 'Keterampilan Umum',
            self::KeterampilanKhusus => 'Keterampilan Khusus',
        };
    }
}
