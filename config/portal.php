<?php

return [
    'creator' => env('PORTAL_CREATOR', 'Kelompok 6 Ilmu Komputer B'),
    'contact_person' => env('PORTAL_CONTACT_PERSON', 'Muhammad Hidayat'),
    'email' => env('PORTAL_CONTACT_EMAIL', 'muhhidayat050607@gmail.com'),
    'phone' => env('PORTAL_CONTACT_PHONE', '+6282187280779'),
    'whatsapp' => env('PORTAL_WHATSAPP', '6282187280779'),

    'team' => [
        ['name' => 'Muhammad Hidayat', 'role' => 'Ketua'],
        ['name' => 'Zolahuddin Ahmad', 'role' => 'Anggota'],
        ['name' => 'Purnama Fatmawansyah T.P.K.', 'role' => 'Anggota'],
        ['name' => 'Fitria Rizky Oktavia', 'role' => 'Anggota'],
        ['name' => 'Siti Munawara', 'role' => 'Anggota'],
    ],

    'socials' => array_filter([
        'instagram' => env('PORTAL_INSTAGRAM'),
        'github' => env('PORTAL_GITHUB'),
        'linkedin' => env('PORTAL_LINKEDIN'),
    ]),
];
