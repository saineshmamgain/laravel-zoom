<?php
namespace CodeZilla\LaravelZoom\Api;

use InvalidArgumentException;

/**
 * File : ZoomUsers.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 3/5/19
 * Time: 11:18 AM
 */


class ZoomUsers {

    public function __construct()
    {
    }

    public function getUsers(string $status = 'active', int $page_number = 1){
        if (!in_array($status,['active','inactive','pending']))
            throw new InvalidArgumentException('Invalid Status.');

        $params = [
            'status' => $status,
            'page_size' => config('laravel-zoom.api.per_page_records'),
            'page_number' => $page_number
        ];

        $params = http_build_query($params);

        $request = new MakeRequest('/users?'.$params);
        return $request->sendRequest();
    }

}