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
        ]
    ]
];
