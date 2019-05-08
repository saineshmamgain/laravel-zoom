<?php
namespace CodeZilla\LaravelZoom\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    /**
     * ZoomMeetings constructor.
     */
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
            'auto_recording' => 'cloud', // none, local, cloud
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
            'type' => 'required'
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
        $requestClass = config('laravel-zoom.classes.make_request');
        $request = new $requestClass($uri . '?' . $params);
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
        $requestClass = config('laravel-zoom.classes.make_request');
        $request = new $requestClass($uri);
        if (!empty($meeting_recurring_settings))
            $meeting_data['recurrence'] = $meeting_recurring_settings;
        if (!empty($meeting_settings))
            $meeting_data['settings'] = $meeting_settings;
        if (!empty($meeting_tracking_fields))
            $meeting_data['tracking_fields'] = $meeting_tracking_fields;
        return $request->setBody($meeting_data, true)->sendRequest(config('laravel-zoom.urls.meetings.create.method'));

    }


    /**
     * @param string $meeting_id
     * @param array $meeting_data
     * @param array $meeting_settings
     * @return array
     *
     * @example : https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingupdate
     */
    protected function updateMeeting(string $meeting_id, array $meeting_data = [], array $meeting_settings = []){
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.meetings.update.uri'));
        $requestClass = config('laravel-zoom.classes.make_request');
        $request = new $requestClass($uri);
        if (!empty($meeting_settings))
            $meeting_data['settings'] = $meeting_settings;
        return $request->setBody($meeting_data, true)->sendRequest(config('laravel-zoom.urls.meetings.update.method'));
    }

    /**
     * @param string $user_id
     * @param string $topic
     * @param string|null $schedule_for
     * @param string|null $password
     * @param string|null $agenda
     * @param array|null $tracking_fields
     * @param array|null $settings
     * @return array
     */
    public function createInstantMeeting(string $user_id, string $topic, string $schedule_for = null, string $password = null, string $agenda = null, array $tracking_fields = null, array $settings = null){

        $validator = Validator::make([
            'user_id' => $user_id,
            'topic' => $topic,
            'password' => $password,
            'agenda' => $agenda,
            'tracking_fields' => $tracking_fields,
            'settings' => $settings,
            'schedule_for' => $schedule_for
        ], [
            'user_id' => 'required',
            'topic' => 'required|min:3',
            'password' => 'nullable',
            'agenda' => 'nullable',
            'tracking_fields' => 'nullable|array',
            'settings' => 'nullable|array',
            'schedule_for' => 'nullable'
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
        if (!empty($data['schedule_for']))
            $meeting_data['schedule_for'] = $data['schedule_for'];
        else
            $meeting_data['schedule_for'] = $user_id;
        if (!empty($data['password']))
            $meeting_data['password'] = $data['password'];
        if (!empty($data['agenda']))
            $meeting_data['agenda'] = $data['agenda'];

        return $this->createMeeting($user_id, $meeting_data, $meeting_settings, $meeting_tracking_fields);
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

        $validator = Validator::make([
            'user_id' => $user_id,
            'topic' => $topic,
            'start_time' => $start_time,
            'duration' => $duration,
            'timezone' => $timezone,
            'schedule_for' => $schedule_for,
            'password' => $password,
            'agenda' => $agenda,
            'tracking_fields' => $tracking_fields,
            'settings' => $settings
        ],[
            'user_id' => 'required',
            'topic' => 'required|min:3',
            'start_time' => [
                'required',
                function ($attribute, $value, $fail){
                    if (!($value instanceof Carbon))
                        $fail($attribute . ' should be a \Carbon\Carbon object.');
                },
                function ($attribute, $value, $fail){
                    if ($value->isPast())
                        $fail($attribute.' cannot be in past.');
                }
            ],
            'password' => 'nullable|string',
            'agenda' => 'nullable|string',
            'tracking_fields' => 'nullable|array',
            'settings' => 'nullable|array',
            'schedule_for' => 'nullable|string',
            'duration' => 'required|numeric',
            'timezone' => [
                'required',
                function($attribute, $value, $fail){
                    if(!in_array($value, timezone_identifiers_list())){
                        $fail($value . ' is not a valid timezone.');
                    }
                }
            ],
        ]);

        if ($validator->fails())
            $this->sendError($validator->errors()->toArray());

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
        $meeting_data['type'] = 2;
        $meeting_data['start_time'] = $data['start_time']->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');;
        $meeting_data['timezone'] = $data['timezone'];
        $meeting_data['duration'] = $data['duration'];


        if (!empty($data['schedule_for']))
            $meeting_data['schedule_for'] = $data['schedule_for'];
        else
            $meeting_data['schedule_for'] = $user_id;

        if (!empty($data['password']))
            $meeting_data['password'] = $data['password'];
        if (!empty($data['agenda']))
            $meeting_data['agenda'] = $data['agenda'];

        return $this->createMeeting($user_id, $meeting_data, $meeting_settings, $meeting_tracking_fields);
    }

    /**
     * @param int $meeting_id
     * @return array
     */
    public function retrieveMeeting(int $meeting_id){
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.meetings.retrieve.uri'));
        $requestClass = config('laravel-zoom.classes.make_request');
        return (new $requestClass($uri))->sendRequest(config('laravel-zoom.urls.meetings.retrieve.method'));
    }

    /**
     * @param int $meeting_id
     * @return array
     */
    public function deleteMeeting(int $meeting_id){
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.meetings.delete.uri'));
        $requestClass = config('laravel-zoom.classes.make_request');
        return (new $requestClass($uri))->sendRequest(config('laravel-zoom.urls.meetings.delete.method'));
    }

    public function updateScheduledMeeting(int $meeting_id, string $agenda = null, Carbon $start_time = null, int $duration = 0, array $settings = []){
        $validator = Validator::make([
            'meeting_id' => $meeting_id,
            'agenda' => $agenda,
            'start_time' => $start_time,
            'duration' => $duration,
            'settings' => $settings
        ],[
            'meeting_id' => 'required|numeric',
            'agenda' => 'nullable|string',
            'start_time' => [
                'nullable',
                function ($attribute, $value, $fail){
                    if (!($value instanceof Carbon))
                        $fail($attribute . ' should be a \Carbon\Carbon object.');
                },
                function ($attribute, $value, $fail){
                    if ($value->isPast())
                        $fail($attribute.' cannot be in past.');
                }
            ],
            'duration' => 'nullable|numeric',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());
        $data = $validator->validated();
        $meeting_data = [];
        $meeting_settings = [];
        if (!empty($data['agenda']))
            $meeting_data['agenda'] = $data['agenda'];
        if (!empty($data['start_time']))
            $meeting_data['start_time'] = $data['start_time']->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        if (!empty($data['duration']))
            $meeting_data['duration'] = $data['duration'];
        if (!empty($data['settings']))
            $meeting_settings = $data['settings'];

        if (empty($meeting_data) && empty($meeting_settings))
            return $this->sendError(['data'=> 'no change in data']);

        return $this->updateMeeting($meeting_id,$meeting_data,$meeting_settings);
    }


}