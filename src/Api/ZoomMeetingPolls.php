<?php
namespace CodeZilla\LaravelZoom\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * File : ZoomMeetingPolls.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 4/5/19
 * Time: 4:53 PM
 */


class ZoomMeetingPolls extends BaseApi{


    /**
     * @param int $meeting_id
     * @return array
     */
    public function getMeetingPolls(int $meeting_id){
        $class = config('laravel-zoom.classes.make_request');
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.polls.list.uri'));
        return (new $class($uri))->sendRequest(config('laravel-zoom.urls.polls.list.method'));
    }

    /**
     * @param int $meeting_id
     * @param string $poll_title
     * @param array $questions
     * @return array
     */
    public function createMeetingPoll(int $meeting_id, string $poll_title, array $questions){
        $requestData = [
            'meeting_id' => $meeting_id,
            'title' => $poll_title,
            'questions' => $questions
        ];
        $validator = Validator::make($requestData, [
            'meeting_id' => 'required|numeric',
            'title' => 'required|string',
            'questions' => 'required|array',
            'questions.*.name' => 'required|string',
            'questions.*.type' => 'required|in:single,multiple',
            'questions.*.answers' => 'required|array',
            'questions.*.answers.*' => 'required',
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());

        $data = $validator->validated();

        $class = config('laravel-zoom.classes.make_request');
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.polls.create.uri'));
        return (new $class($uri))->setBody([
            'title' => $data['title'],
            'questions' => $data['questions']
        ], true)->sendRequest(config('laravel-zoom.urls.polls.create.method'));
    }

    /**
     * @param int $meeting_id
     * @param string $poll_id
     * @param string $poll_title
     * @param array $questions
     * @return array
     */
    public function updateMeetingPoll(int $meeting_id, string $poll_id, string $poll_title, array $questions){
        $requestData = [
            'meeting_id' => $meeting_id,
            'title' => $poll_title,
            'questions' => $questions
        ];
        $validator = Validator::make($requestData, [
            'meeting_id' => 'required|numeric',
            'title' => 'required|string',
            'questions' => 'required|array',
            'questions.*.name' => 'required|string',
            'questions.*.type' => 'required|in:single,multiple',
            'questions.*.answers' => 'required|array',
            'questions.*.answers.*' => 'required',
        ]);

        if ($validator->fails())
            return $this->sendError($validator->errors()->toArray());

        $data = $validator->validated();

        $class = config('laravel-zoom.classes.make_request');
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.polls.update.uri'));
        $uri = Str::replaceFirst('{poll_id}', $poll_id, $uri);
        return (new $class($uri))->setBody([
            'title' => $data['title'],
            'questions' => $data['questions']
        ], true)->sendRequest(config('laravel-zoom.urls.polls.update.method'));
    }


    /**
     * @param int $meeting_id
     * @param string $poll_id
     * @return array
     */
    public function deleteMeetingPoll(int $meeting_id, string $poll_id){
        $class = config('laravel-zoom.classes.make_request');
        $uri = Str::replaceFirst('{meeting_id}', $meeting_id, config('laravel-zoom.urls.polls.delete.uri'));
        $uri = Str::replaceFirst('{poll_id}', $poll_id, $uri);
        return (new $class($uri))->sendRequest(config('laravel-zoom.urls.polls.delete.method'));
    }
}