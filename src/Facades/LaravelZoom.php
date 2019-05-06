<?php
namespace CodeZilla\LaravelZoom\Facades;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
  * File : LaravelZoom.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 4:46 PM
  */

/**
 * Class LaravelZoom
 * @package CodeZilla\LaravelZoom\Facade
 * @method static getJWTToken(int $zoom_jwt_expires = 0)
 * @method static generateSignature(int $meeting_number)
 * @method static getUsers(string $status = 'active', int $page_number = 1)
 * @method static getMeetings(string $user_id, string $type = 'live', int $page_number = 1)
 * @method static createInstantMeeting(string $user_id, string $topic, string $password = null, string $agenda = null, array $tracking_fields = null, array $settings = null)
 * @method static createScheduledMeeting(string $user_id, string $topic, Carbon $start_time, int $duration = 60, string $timezone = 'Asia/Kolkata', string $schedule_for = null, string $password = null, string $agenda = null, array $tracking_fields = null, array $settings = null)
 * @method static retrieveMeeting(int $meeting_id)
 * @method static deleteMeeting(int $meeting_id)
 * @method static updateScheduledMeeting(int $meeting_id, string $agenda = null, Carbon $start_time = null, int $duration = 0, array $settings = [])
 * @method static getMeetingPolls(int $meeting_id)
 * @method static createMeetingPoll(int $meeting_id, string $poll_title, array $questions)
 */

class LaravelZoom extends Facade{

    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'laravelzoom';
    }

}