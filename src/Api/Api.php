<?php
namespace CodeZilla\LaravelZoom\Api;


use GuzzleHttp\Psr7\Request;

/**
 * File : Api.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 2/5/19
 * Time: 6:23 PM
 */


class Api {

    const API_URL = 'https://api.zoom.us/v2';

    public function sendRequest(){
        $request = new Request('GET', self::API_URL . '/users', [
            'User-Agent' => 'Zoom-Jwt-Request',
            'content-type' => 'application/json',
            'Auth' => 'Bearer '. env('ZOOM_JWT_TOKEN')
        ]);

        $body = $request->getBody();

        var_dump($body);die;
    }

}