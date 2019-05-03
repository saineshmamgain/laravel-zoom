<?php
namespace CodeZilla\LaravelZoom\Api;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * File : ZoomMeetings.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 3/5/19
 * Time: 12:15 PM
 */


class ZoomMeetings {

    public function __construct()
    {
    }

    public function getMeetings(string $user_id, string $type = 'live', int $page_number = 1){
        if (!in_array($type,['live','scheduled','upcoming']))
            throw new InvalidArgumentException('Invalid Meeting Type.');

        $params = [
            'type' => $type,
            'page_size' => config('laravel-zoom.api.per_page_records'),
            'page_number' => $page_number
        ];

        $params = http_build_query($params);

        $uri = Str::replaceFirst('{user_id}', $user_id, config('laravel-zoom.meetings.list.uri'));

        $request = new MakeRequest($uri . '?' . $params);
        return $request->sendRequest(config('laravel-zoom.meetings.list.method'));
    }
}