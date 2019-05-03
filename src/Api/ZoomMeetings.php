<?php
namespace CodeZilla\LaravelZoom\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * File : ZoomMeetings.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 3/5/19
 * Time: 12:15 PM
 */


class ZoomMeetings extends BaseApi {

    protected $defaultMeetingSettings;
    protected $defaultMeetingRecurrenceSettings;

    public function __construct()
    {
        $this->defaultMeetingSettings = [
            'host_video' => false,
            'participant_video' => false,
            'cn_meeting' => false,
            'in_meeting' => true,
            'join_before_host' => false,
            'mute_upon_entry' => true,
            'watermark' => false,
            'use_pmi' => false,
            'approval_type' => 2, // 0-automatic, 1-manually, 2-not required
            'registration_type' => 3, // 1-Attendees register once and can attend any of the occurrences., 2-Attendees need to register for each occurrence to attend., 3-Attendees register once and can choose one or more occurrences to attend.
            'audio' => 'voip', // voip, telephony, both
            'auto_recording' => 'none', // none, local, cloud
            'enforce_login' => false,
            'enforce_login_domains' => null,
            'alternative_hosts' => null,
            'close_registration' => false,
            'waiting_room' => false,
            'global_dial_in_countries' => [],
            'contact_name' => null,
            'contact_email' => null
        ];

        $this->defaultMeetingRecurrenceSettings = [
            'type' => 1, // 1-Daily, 2-Weekly, 3-Monthly
            'repeat_interval' => 90,
            'weekly_days' => '1,2,3,4,5,6,7', // 1-Sunday, 2-Monday, 3-Tuesday, 4-Wednesday, 5-Thursday, 6-Friday, 7-Saturday
            'monthly_day' => 1,
            'monthly_week' => 1, // -1-Last Week, 1-First Week, 2-Second Week, 3-Third Week, 4-Fourth Week
            'monthly_week_day' => 2, // 1-Sunday, 2-Monday, 3-Tuesday, 4-Wednesday, 5-Thursday, 6-Friday, 7-Saturday
            'end_times' => 10,
            'end_date_time' => now()->tz('UTC')->format('yyyy-MM-ddTHH:mm:ssZ')
        ];
    }

    /**
     * @param string $user_id
     * @param string $type
     * @param int $page_number
     * @return array
     *
     * @example : https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetings
     */
    public function getMeetings(string $user_id, string $type = 'live', int $page_number = 1){

        $validator = Validator::make([
            'user_id' => $user_id,
            'type' => $type,
        ],[
            'user_id' => 'required|string',
            'type' => 'required|in:live,scheduled,upcoming'
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());


        $params = [
            'type' => $type,
            'page_size' => config('laravel-zoom.api.per_page_records'),
            'page_number' => $page_number
        ];
        $params = http_build_query($params);
        $uri = Str::replaceFirst('{user_id}', $user_id, config('laravel-zoom.urls.meetings.list.uri'));
        $request = new MakeRequest($uri . '?' . $params);
        return $request->sendRequest(config('laravel-zoom.urls.meetings.list.method'));
    }

    /**
     * @param string $user_id
     * @param array $meeting_data
     * @param array $meeting_settings
     * @param array $meeting_tracking_fields
     * @param array $meeting_recurring_settings
     * @return array
     *
     * @example : https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate
     */
    protected function createMeeting(string $user_id, array $meeting_data = [], array $meeting_settings = [], array $meeting_tracking_fields = [], array $meeting_recurring_settings = []){
        $uri = Str::replaceFirst('{user_id}', $user_id, config('laravel-zoom.urls.meetings.create.uri'));
        $request = new MakeRequest($uri);
        if (!empty($meeting_recurring_settings))
            $meeting_data['recurrence'] = $meeting_recurring_settings;
        if (!empty($meeting_settings))
            $meeting_data['settings'] = $meeting_settings;
        if (!empty($meeting_tracking_fields))
            $meeting_data['tracking_fields'] = $meeting_tracking_fields;

        return $request->setBody($meeting_data, true)->sendRequest(config('laravel-zoom.urls.meetings.create.method'));

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

        $validator = Validator::make([
            'topic' => $topic,
            'password' => $password,
            'agenda' => $agenda,
            'tracking_fields' => $tracking_fields,
            'settings' => $settings
        ], [
            'topic' => 'required|min:3',
            'password' => 'nullable',
            'agenda' => 'nullable',
            'tracking_fields' => 'nullable|array',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());

        $data = $validator->validated();

        if (empty($data['settings']))
            $meeting_settings = $this->defaultMeetingSettings;
        else
            $meeting_settings = array_merge($this->defaultMeetingSettings, $data['settings']);

        if (empty($data['tracking_fields']))
            $meeting_tracking_fields = [];
        else
            $meeting_tracking_fields = $data['tracking_fields'];

        $meeting_data['topic'] = $data['topic'];
        $meeting_data['type'] = 1;
        if (!empty($meeting_data['password']))
            $meeting_data['password'] = $data['password'];
        if (!empty($data['agenda']))
            $meeting_data['agenda'] = $data['agenda'];

        return $this->createMeeting($user_id, $meeting_data, $meeting_settings, $meeting_tracking_fields);
    }
}