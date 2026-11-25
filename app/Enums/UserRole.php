<?php

namespace App\Enums;

enum UserRole: string
{
    case MAHASISWA = 'mahasiswa';
    case ASISTEN = 'asisten';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::MAHASISWA => 'Mahasiswa',
            self::ASISTEN => 'Asisten Praktikum',
            self::ADMIN => 'Administrator',
        };
    }
}
