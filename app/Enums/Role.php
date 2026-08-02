<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super-admin';
    case AdminUniv = 'admin-univ';
    case AdminFakultas = 'admin-fakultas';
    case AdminProdi = 'admin-prodi';
    case Kaprodi = 'kaprodi';
    case Reviewer = 'reviewer';
    case Dosen = 'dosen';
    case Lpm = 'lpm';
    case Mahasiswa = 'mahasiswa';
}
