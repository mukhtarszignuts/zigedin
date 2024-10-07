<?php

return [
    'ADMIN_EMAIL'       =>  env('ADMIN_EMAIL', 'admin@gmail.com'), // Admin email address
    'ADMIN_PASSWORD'    =>  env('ADMIN_PASSWORD', 'Admin@123'), // Admin password


    'FRONTEND_URL'                      => env('FRONTEND_URL'),
    'FRONTEND_FORGET_PASSWORD_URL'      => env('FRONTEND_FORGET_PASSWORD_URL'),

    'USER_ROLE' => [
        'admin'      => 'A', // Role code for admin users
        'employer'   => 'E', // Role code for employer users
        'candidate'  => 'C', // Role code for candidate users
    ],
];
