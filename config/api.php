<?php

return [
    'providers' => [
        [
            'url' => 'http://interview-api.stage1.beecoded.ro/mock/provider1/email',
            'api_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIyIiwiaWF0IjoxNzAyMzk3NzAwLCJleHAiOjE3MDI0ODQxMDB9.8ZXXcEo1jMvJ3i6xC-n4XxCYPPlAVf1hYQNZbkk5yhM',
        ],
        [
            'url' => 'http://interview-api.stage1.beecoded.ro/mock/provider2/email',
            'api_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIyIiwiaWF0IjoxNzAyMzk3NzAwLCJleHAiOjE3MDI0ODQxMDB9.8ZXXcEo1jMvJ3i6xC-n4XxCYPPlAVf1hYQNZbkk5yhM',
        ],
        [
            'url' => 'http://interview-api.stage1.beecoded.ro/mock/provider2/email',
            'api_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIyIiwiaWF0IjoxNzAyMzk3NzAwLCJleHAiOjE3MDI0ODQxMDB9.8ZXXcEo1jMvJ3i6xC-n4XxCYPPlAVf1hYQNZbkk5yhM',
        ]
    ],
    'auth_url' => 'http://interview-api.stage1.beecoded.ro/auth/login',
    'auth_credentials' => [
        'email' => env('API_USER', 'beecoded@test.com'),
        'password' => env('API_PASSWORD', '0sqwDFe16WTy'),
    ],
];
