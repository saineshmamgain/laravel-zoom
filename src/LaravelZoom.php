<?php
namespace CodeZilla\LaravelZoom;

use Carbon\Carbon;
use CodeZilla\LaravelZoom\Api\ZoomMeetings;
use CodeZilla\LaravelZoom\Api\ZoomUsers;

/**
  * File : LaravelZoom.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 4:47 PM
  */


class LaravelZoom {

    private $key;
    private $secret;

    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function generateSignature($meeting_number, $role = 0){
        $time = time() * 1000;
        $data = base64_encode($this->key . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $this->secret, true);
        $_sig = $this->key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    public function getJWTToken(int $zoom_jwt_expires = 0){
        if ($zoom_jwt_expires == 0)
            $zoom_jwt_expires = (new Carbon())->addDays(30)->unix();
        return JWT::generate($this->key, $this->secret, ["alg" => "HS256","typ" => "JWT"], ["iss"=> $this->key,"exp"=> $zoom_jwt_expires]);
    }

    public function getUsers(string $status = 'active', int $page_number = 1){
        return (new ZoomUsers())->getUsers($status, $page_number);
    }

    public function getMeetings(string $user_id, string $type = 'live', int $page_number = 1){
        return (new ZoomMeetings())->getMeetings($user_id, $type, $page_number);
    }
}