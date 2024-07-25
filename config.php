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
            'nama_menu' => 'Pengeluaran',
            'icon' => '<i class="fas fa-money-bill-wave-alt"></i>',
            'url' => 'pengeluaran',
            'access' => ['admin', 'user']
        ],
        [
            'nama_menu' => 'Setoran',
            'icon' => '<i class="fas fa-hand-holding-usd"></i>',
            'url' => 'setoran',
            'access' => ['admin', 'user']
        ],
        [
            'nama_menu' => 'Laporan',
            'icon' => '<i class="fas fa-folder-open"></i>',
            'url' => 'report',
            'access' => ['admin', 'user']
        ]
    ]
];
