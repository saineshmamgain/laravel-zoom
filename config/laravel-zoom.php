<?php

/**
  * File : laravel-zoom.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 5:30 PM
  */


use CodeZilla\LaravelZoom\Api\MakeRequest;
use CodeZilla\LaravelZoom\Api\ZoomMeetingPolls;
use CodeZilla\LaravelZoom\Api\ZoomMeetings;
use CodeZilla\LaravelZoom\Api\ZoomRecordings;
use CodeZilla\LaravelZoom\Api\ZoomUsers;
use CodeZilla\LaravelZoom\Commands\JWTCommand;
use CodeZilla\LaravelZoom\JWT;
use CodeZilla\LaravelZoom\LaravelZoom;

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
            'create' => [
                'method' => 'post',
                'uri' => '/users'
            ],
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
            ],
            'get_past_meeting' => [
                'method' => 'get',
                'uri' => 'past_meetings/{meeting_uuid}'
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
            ],
            'update' => [
                'method' => 'put',
                'uri' => '/meetings/{meeting_id}/polls/{poll_id}'
            ],
            'delete' => [
                'method' => 'delete',
                'uri' => '/meetings/{meeting_id}/polls/{poll_id}'
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
        'zoom_users' => ZoomUsers::class,
        'zoom_meetings' => ZoomMeetings::class,
        'zoom_meeting_polls' => ZoomMeetingPolls::class,
        'zoom_recordings' => ZoomRecordings::class,
        'make_request' => MakeRequest::class,
        'jwt_command' => JWTCommand::class,
        'jwt' => JWT::class,
        'laravel_zoom' => LaravelZoom::class,
    ],

    'default_user_id' => null
];