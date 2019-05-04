<?php
namespace CodeZilla\LaravelZoom;

use Carbon\Carbon;

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

    /**
     * LaravelZoom constructor.
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param $meeting_number
     * @param int $role
     * @return string
     */
    public function generateSignature($meeting_number, $role = 0){
        $time = time() * 1000;
        $data = base64_encode($this->key . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $this->secret, true);
        $_sig = $this->key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    /**
     * @param int $zoom_jwt_expires
     * @return mixed|string
     * @throws \Exception
     */
    public function getJWTToken(int $zoom_jwt_expires = 0){
        if ($zoom_jwt_expires == 0)
            $zoom_jwt_expires = (new Carbon())->addDays(30)->unix();
        $jwtClass = config('laravel-zoom.classes.jwt');
        return $jwtClass::generate($this->key, $this->secret, ["alg" => "HS256","typ" => "JWT"], ["iss"=> $this->key,"exp"=> $zoom_jwt_expires]);
    }

    /**
     * @param string $status
     * @param int $page_number
     * @return array
     */
    public function getUsers(string $status = 'active', int $page_number = 1){
        $class = config('laravel-zoom.classes.zoom_users');
        return (new $class())->getUsers($status, $page_number);
    }

    /**
     * @param string $user_id
     * @param string $type
     * @param int $page_number
     * @return array
     */
    public function getMeetings(string $user_id, string $type = 'live', int $page_number = 1){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->getMeetings($user_id, $type, $page_number);
    }

    /**
     * @param string $user_id
     * @param string $topic
     * @param string|null $password
     * @param string|null $agenda
     * @param array|null $tracking_fields
     * @param array|null $settings
     * @return array
     */
    public function createInstantMeeting(string $user_id, string $topic, string $password = null, string $agenda = null, array $tracking_fields = null, array $settings = null){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->createInstantMeeting($user_id, $topic, $password, $agenda, $tracking_fields, $settings);
    }

    /**
     * @param string $user_id
     * @param string $topic
     * @param Carbon $start_time
     * @param int $duration
     * @param string $timezone
     * @param string|null $schedule_for
     * @param string|null $password
     * @param string|null $agenda
     * @param array|null $tracking_fields
     * @param array|null $settings
     * @return array
     */
    public function createScheduledMeeting(string $user_id, string $topic, Carbon $start_time, int $duration = 60, string $timezone = 'Asia/Kolkata', string $schedule_for = null, string $password = null, string $agenda = null, array $tracking_fields = null, array $settings = null){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->createScheduledMeeting($user_id, $topic, $start_time, $duration, $timezone, $schedule_for, $password, $agenda, $tracking_fields,$settings);
    }

    /**
     * @param int $meeting_id
     * @return array
     */
    public function retrieveMeeting(int $meeting_id){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->retrieveMeeting($meeting_id);
    }

    /**
     * @param int $meeting_id
     * @return array
     */
    public function deleteMeeting(int $meeting_id){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->deleteMeeting($meeting_id);
    }

    public function updateScheduledMeeting(int $meeting_id, string $agenda = null, Carbon $start_time = null, int $duration = 0, array $settings = []){
        $class = config('laravel-zoom.classes.zoom_meetings');
        return (new $class())->updateScheduledMeeting($meeting_id, $agenda, $start_time, $duration, $settings);
    }
}