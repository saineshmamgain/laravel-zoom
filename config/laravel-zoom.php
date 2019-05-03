<?php

/**
  * File : laravel-zoom.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 5:30 PM
  */


return [

    'zoom_api_key' => env('ZOOM_API_KEY'),

    'zoom_api_secret' => env('ZOOM_API_SECRET'),

    'zoom_jwt_token' => env('ZOOM_JWT_TOKEN'),

    'zoom_jwt_expires_on' => env('ZOOM_JWT_EXPIRES_ON'),

    'api' => [
        'base_url' => 'https://api.zoom.us/v2',
        'per_page_records' => 30,
    ],

    'urls' => [
        'users' => [
            'list' => [
                'method' => 'get',
                'uri' => '/users'
            ]
        ],
        'meetings' => [
            'list' => [
                'method' => 'get',
                'uri' => '/users/{user_id}/meetings',
            ],
            'create' => [
                'method' => 'post',
                'uri' => '/users/{user_id}/meetings'
            ]
        ]
    ],

    'default_user_id' => 'fTJCLJ7lSg6Ywh869X8h6w'
];