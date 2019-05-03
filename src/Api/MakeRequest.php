<?php
namespace CodeZilla\LaravelZoom\Api;


use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;

/**
 * File : Api.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 2/5/19
 * Time: 6:23 PM
 */


class MakeRequest {

    protected $headers;
    protected $base_url;
    protected $url;
    protected $client;
    protected $body;
    protected $isJSON;

    public function __construct(string $url = null, array $headers = null, string $base_url = null)
    {
        if (empty($headers)){
            $this->headers = [
                'User-Agent' => 'Zoom-Jwt-Request',
                'content-type' => 'application/json',
                'Authorization' => 'Bearer '. env('ZOOM_JWT_TOKEN')
            ];
        }else{
            $this->headers = $headers;
        }

        if (empty($base_url))
            $this->base_url = config('laravel-zoom.api.base_url');
        else
            $this->base_url = $base_url;

        $this->url = $url;
    }

    public function setHeaders(array $headers){
        $this->headers = $headers;
        return $this;
    }

    public function setBaseUrl(string $base_url){
        $this->base_url = $base_url;
        return $this;
    }

    public function setUrl(string $url){
        $this->url = $url;
        return $this;
    }

    public function setBody($body, bool $json = false){
        $this->body = $body;
        $this->isJSON = $json;
        return $this;
    }

    public function sendRequest(string $requestMethod = 'get'){
        if (empty($this->url))
            throw new InvalidArgumentException('API URL is Required.');
        $requestMethod = strtolower($requestMethod);
        if (!in_array($requestMethod, [
            'get',
            'post',
            'put',
            'patch',
            'delete',
            'option'
        ])){
            throw new InvalidArgumentException('Invalid Request Method.');
        }
        $this->client = new Client([
            'base_uri' => $this->base_url,
            'http_errors' => false
        ]);
        $options = [
            'headers' => $this->headers
        ];
        if (!empty($this->body)){
            if ($this->isJSON){
                $options['json'] = $this->body;
            }else{
                $options['body'] = $this->body;
            }
        }
        try{
            $request = $this->client->request($requestMethod, $this->base_url . $this->url, $options);
            $status = $request->getStatusCode();
            $body = $request->getBody()->getContents();
            $body = $this->checkAndReturnJSON($body);
            return ['status' => $status, 'body' => $body];
        }catch (Exception $exception){
            return ['status' => 0, 'body' => $exception->getMessage()];
        }
    }

    public function checkAndReturnJSON($str) {
        $json = json_decode($str);
        if ($json && $str != $json)
            return $json;
        return $str;
    }

}