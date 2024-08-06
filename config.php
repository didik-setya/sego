<?php

return [
    'menu' => [
        [
            'nama_menu' => 'Dashboard',
            'icon' => '<i class="fas fa-tachometer-alt"></i>',
            'url' => 'dashboard',
            'access' => ['admin', 'user']
        ],

        [
            'nama_menu' => 'Mangement Kost',
            'icon' => '<i class="fas fa-home"></i>',
            'url' => 'kost',
            'access' => ['admin']
        ],
        [
            'nama_menu' => 'User & Access Kost',
            'icon' => '<i class="fas fa-house-user"></i>',
            'url' => 'access',
            'access' => ['admin']
        ],

        [
            'nama_menu' => 'Management Kamar',
            'icon' => '<i class="fas fa-hotel"></i>',
            'url' => 'kamar',
            'access' => ['admin', 'user']
        ],
        [
            'nama_menu' => 'Management Penghuni',
            'icon' => '<i class="fas fa-users"></i>',
            'url' => 'penghuni',
            'access' => ['admin', 'user']
        ],

        [
            'nama_menu' => 'Transaksi',
            'icon' => '<i class="fas fa-folder-open"></i>',
            'url' => 'transaction',
            'access' => ['admin', 'user']
        ],



    ]
];
