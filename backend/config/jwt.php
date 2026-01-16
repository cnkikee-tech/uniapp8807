<?php

return [
    // JWT密钥
    'secret' => env('jwt.secret', 'your-secret-key-here'),
    
    // JWT过期时间（秒）
    'expire' => env('jwt.expire', 7200),
    
    // JWT刷新时间（秒）
    'refresh_expire' => env('jwt.refresh_expire', 604800),
    
    // JWT算法
    'algorithm' => env('jwt.algorithm', 'HS256'),
    
    // JWT签发者
    'issuer' => env('jwt.issuer', 'business-card-system'),
    
    // JWT受众
    'audience' => env('jwt.audience', 'business-card-users'),
    
    // JWT主题
    'subject' => env('jwt.subject', 'business-card-auth'),
    
    // JWT头部
    'header' => [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ],
];