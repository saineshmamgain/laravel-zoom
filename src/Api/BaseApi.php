<?php
namespace CodeZilla\LaravelZoom\Api;

/**
 * File : BaseApi.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 3/5/19
 * Time: 1:12 PM
 */


abstract class BaseApi
{

    public function sendError(array $errors)
    {
        return ['status' => 0, 'body' => $errors];
    }

}