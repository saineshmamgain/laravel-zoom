<?php
namespace CodeZilla\LaravelZoom\Commands;

use Base64Url\Base64Url;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        $actions = [];

        if (empty($api_key)){
            $api_key = $this->ask('Enter Zoom API Key: ');
            $actions[] = function () use (&$api_key){
                $this->changeEnv(['ZOOM_API_KEY', $api_key]);
            };
        }

        if (empty($api_secret)){
            $api_secret = $this->ask('Enter Zoom API Secret: ');
            $actions[] = function () use (&$api_secret){
                $this->changeEnv(['ZOOM_API_SECRET', $api_secret]);
            };
        }

        $header = Base64Url::encode(json_encode(["alg" => "HS256","typ" => "JWT"]));
        $payload = Base64Url::encode(json_encode(["iss"=> $api_key,"exp"=> (new Carbon())->addDays($days)->addHours($hours)->unix()]));

        $data = "$header.$payload";
        $signature = hash_hmac('sha256', $data, $api_secret,true);
        $signature = Base64Url::encode($signature);
        $jwt = "$data.$signature";

        $actions[] = function () use (&$jwt){
            $this->changeEnv(['ZOOM_JWT', $jwt]);
        };

        $bar = $this->output->createProgressBar(count($actions));

        $bar->start();

        foreach ($actions as $action) {
            $this->doAction($action);
            $bar->advance();
        }

        $bar->finish();
    }

    private function doAction($action){
        echo var_export($action);
        $action();
    }

    private function changeEnv($data = array()){
        if(count($data) > 0){
            $env = file_get_contents(base_path() . '/.env');
            $env = preg_split('/\s+/', $env);;
            foreach((array)$data as $key => $value){
                foreach($env as $env_key => $env_value){
                    $entry = explode("=", $env_value, 2);
                    if($entry[0] == $key){
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        $env[$env_key] = $env_value;
                    }
                }
            }
            $env = implode("\n", $env);
            file_put_contents(base_path() . '/.env', $env);
            return true;
        } else {
            return false;
        }
    }
}