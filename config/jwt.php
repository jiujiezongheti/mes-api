<?php

return [
    'admin' => [
        'secret' => getenv('JWT_SECRET_ADMIN') ?: 'admin-secret-key',
        'expire' => (int)(getenv('JWT_EXPIRE') ?: 86400),
        'refresh_expire' => (int)(getenv('JWT_REFRESH_EXPIRE') ?: 604800),
        'iss' => 'mes-admin',
    ],
    'mobile' => [
        'secret' => getenv('JWT_SECRET_MOBILE') ?: 'mobile-secret-key',
        'expire' => (int)(getenv('JWT_EXPIRE') ?: 86400),
        'refresh_expire' => (int)(getenv('JWT_REFRESH_EXPIRE') ?: 604800),
        'iss' => 'mes-mobile',
    ],
    'api' => [
        'secret' => getenv('JWT_SECRET_API') ?: 'api-secret-key',
        'expire' => (int)(getenv('JWT_EXPIRE') ?: 86400),
        'refresh_expire' => (int)(getenv('JWT_REFRESH_EXPIRE') ?: 604800),
        'iss' => 'mes-api',
    ],
];
