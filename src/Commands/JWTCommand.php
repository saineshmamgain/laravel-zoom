<?php
namespace CodeZilla\LaravelZoom\Commands;

use Carbon\Carbon;
use CodeZilla\LaravelZoom\JWT;
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

    protected $signature = 'zoom:jwt-generate {api_key? : Zoom API Key} {api_secret? : Zoom API Secret} {--D|days=0 : Validity of the token in days. Defaults to 0} {--H|hours=1 : Validity of token in Hours. Defaults to 1}';
    protected $description = 'Generate Zoom JWT Token.';

    protected $editor;

    /**
     * JWTCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
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

        $jwtClass = config('laravel-zoom.classes.jwt');

        $jwtClass::generate($api_key, $api_secret, ["alg" => "HS256","typ" => "JWT"], ["iss"=> $api_key,"exp"=> $zoom_jwt_expires]);
    }


}