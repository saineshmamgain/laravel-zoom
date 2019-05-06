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
            ],
            'retrieve' => [
                'method' => 'get',
                'uri' => '/users/{user_id}'
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
            ],
            'retrieve' => [
                'method' => 'get',
                'uri' => '/meetings/{meeting_id}'
            ],
            'delete' => [
                'method' => 'delete',
                'uri' => '/meetings/{meeting_id}'
            ],
            'update' => [
                'method' => 'patch',
                'uri' => '/meetings/{meeting_id}'
            ]
        ],
        'polls' => [
            'list' => [
                'method' => 'get',
                'uri' => '/meetings/{meeting_id}/polls'
            ],
            'create' => [
                'method' => 'post',
                'uri' => '/meetings/{meeting_id}/polls'
            ]
        ],
        'recordings' => [
            'list' => [
                'method' => 'get',
                'uri' => '/users/{user_id}/recordings'
            ]
        ]
    ],

    'classes' => [
        'zoom_users' => \CodeZilla\LaravelZoom\Api\ZoomUsers::class,
        'zoom_meetings' => \CodeZilla\LaravelZoom\Api\ZoomMeetings::class,
        'zoom_meeting_polls' => \CodeZilla\LaravelZoom\Api\ZoomMeetingPolls::class,
        'zoom_recordings' => \CodeZilla\LaravelZoom\Api\ZoomRecordings::class,
        'make_request' => \CodeZilla\LaravelZoom\Api\MakeRequest::class,
        'jwt_command' => \CodeZilla\LaravelZoom\Commands\JWTCommand::class,
        'jwt' => \CodeZilla\LaravelZoom\JWT::class,
        'laravel_zoom' => \CodeZilla\LaravelZoom\LaravelZoom::class,
    ],

    'default_user_id' => 'fTJCLJ7lSg6Ywh869X8h6w'
];