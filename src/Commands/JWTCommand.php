<?php
namespace CodeZilla\LaravelZoom\Commands;

use Base64Url\Base64Url;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * File : JWTCommand.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 2/5/19
 * Time: 10:55 AM
 */


class JWTCommand extends Command{

    protected $signature = 'zoom:jwt {api_key?} {api_secret?} {--D|days=0 : Validity of the token in days. Defaults to 0} {--H|hours=1 : Validity of token in Hours. Defaults to 1}';
    protected $description = 'Generate Zoom JWT Token.';

    protected $editor;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){
        $days = $this->option('days');
        $hours = $this->option('hours');

        $api_key = $this->argument('api_key');
        $api_secret = $this->argument('api_secret');

        if (empty($api_key))
            $api_key = config('laravel-zoom.zoom_api_key');

        if (empty($api_secret))
            $api_secret = config('laravel-zoom.zoom_api_secret');

        if (empty($api_key))
            $api_key = $this->ask('Enter Zoom API Key: ');

        if (empty($api_secret))
            $api_secret = $this->ask('Enter Zoom API Secret: ');

        $zoom_jwt_expires = (new Carbon())->addDays($days)->addHours($hours)->unix();

        $header = Base64Url::encode(json_encode(["alg" => "HS256","typ" => "JWT"]));
        $payload = Base64Url::encode(json_encode(["iss"=> $api_key,"exp"=> $zoom_jwt_expires]));
        $data = "$header.$payload";
        $signature = hash_hmac('sha256', $data, $api_secret,true);
        $signature = Base64Url::encode($signature);
        $jwt = "$data.$signature";

        $this->changeEnv([
            'ZOOM_API_KEY' => $api_key,
            'ZOOM_API_SECRET' => $api_secret,
            'ZOOM_JWT' => $jwt,
            'ZOOM_JWT_EXPIRES_ON' => $zoom_jwt_expires
        ]);
    }

    private function changeEnv($data = array()){
        if(count($data) > 0){
            $env = file_get_contents(base_path() . '/.env');
            $env = explode("\n", $env);
            $envArray = [];
            foreach ($env as $key => $item) {
                $item = explode('=', $item, 2);
                $envKey = isset($item[0])?trim($item[0]):'';
                $envValue = isset($item[1])?trim($item[1]):'';
                if (!empty($envKey))
                    $envArray[$envKey] = $envValue;
                else
                    $envArray['WHITE_SPACE_'.$key] = "\n";
            }
            foreach ($data as $key => $datum) {
                $envArray[strtoupper($key)] = $datum;
            }
            $newEnv = "";
            foreach ($envArray as $key => $item) {
                if (Str::startsWith($key, 'WHITE_SPACE_')){
                    $newEnv .= "\n";
                }else{
                    $newEnv .= $key."=".$item."\n";
                }
            }
            file_put_contents(base_path() . '/.env', $newEnv);
            return true;
        } else {
            return false;
        }
    }
}