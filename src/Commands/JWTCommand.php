<?php
namespace CodeZilla\LaravelZoom\Commands;

use Illuminate\Console\Command;

/**
 * File : JWTCommand.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 2/5/19
 * Time: 10:55 AM
 */


class JWTCommand extends Command{

    protected $signature = 'zoom:jwt {--D|days=0 : Validity of the token in days. Defaults to 0} {--H|hours=1 : Validity of token in Hours. Defaults to 1}';
    protected $description = 'Generate Zoom JWT Token.';

    protected $editor;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){
        $days = $this->option('days');
        $hours = $this->option('hours');

        $api_key = config('laravel-zoom.zoom_api_key');
        $api_secret = config('laravel-zoom.zoom_api_secret');

        if (empty($api_key))
            $api_key = $this->ask('Enter Zoom API Key: ');

        if (empty($api_secret))
            $api_secret = $this->ask('Enter Zoom API Secret: ');


    }
}