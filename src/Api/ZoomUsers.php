<?php
namespace CodeZilla\LaravelZoom\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * File : ZoomUsers.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 3/5/19
 * Time: 11:18 AM
 */


class ZoomUsers extends BaseApi {

    public function __construct()
    {
    }

    public function getUsers(string $status = 'active', int $page_number = 1){
        $validator = Validator::make([
            'status' => $status
        ],[
            'status' => 'required|in:active,inactive,pending'
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());

        $params = [
            'status' => $status,
            'page_size' => config('laravel-zoom.api.per_page_records'),
            'page_number' => $page_number
        ];

        $params = http_build_query($params);
        $class = config('laravel-zoom.classes.make_request');
        $request = new $class(config('laravel-zoom.urls.users.list.uri') . '?' . $params);
        return $request->sendRequest(config('laravel-zoom.urls.users.list.method'));
    }


    public function retrieveUser(string $user_id){
        $uri = Str::replaceFirst('{user_id}', $user_id, config('laravel-zoom.urls.users.retrieve.uri'));
        $class = config('laravel-zoom.classes.make_request');
        $request = new $class($uri);
        return $request->sendRequest(config('laravel-zoom.urls.users.retrieve.method'));
    }

    public function createUser(string $email, string $first_name, string $last_name, string $password = null, int $type = 1, string $action = 'create'){
        $class = config('laravel-zoom.classes.make_request');
        $request = new $class(config('laravel-zoom.urls.users.create.uri'));
        $user_data = [
            'action' => $action,
            'user_info' => [
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'password' => $password,
                'type' => $type
            ],
        ];
        return $request->setBody($user_data, true)->sendRequest(config('laravel-zoom.urls.users.create.method'));
    }
}