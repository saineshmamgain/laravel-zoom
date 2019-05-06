<?php
namespace CodeZilla\LaravelZoom\Api;

use Illuminate\Support\Str;

/**
 * File : ZoomRecordings.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 6/5/19
 * Time: 11:06 AM
 */


class ZoomRecordings extends BaseApi{

    /**
     * @param string $user_id
     * @param int $page_number
     * @return array
     */
    public function getRecordings(string $user_id, int $page_number = 1){
        $params = [
            'page_size' => config('laravel-zoom.api.per_page_records'),
            'page_number' => $page_number
        ];

        $params = http_build_query($params);
        $class = config('laravel-zoom.classes.make_request');
        $uri = Str::replaceFirst('{user_id}', $user_id, config('laravel-zoom.urls.recordings.list.uri'));
        return (new $class($uri . '?' . $params))->sendRequest(config('laravel-zoom.urls.recordings.list.method'));
    }



}