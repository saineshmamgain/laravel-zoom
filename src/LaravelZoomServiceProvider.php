<?php
namespace CodeZilla\LaravelZoom;
use CodeZilla\LaravelZoom\Commands\JWTCommand;
use Illuminate\Support\ServiceProvider;

/**
  * File : LaravelZoomServiceProvider.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 4:42 PM
  */


class LaravelZoomServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-zoom.php', 'laravel-zoom'
        );
        $this->app->bind('laravelzoom', function (){
            return new LaravelZoom(config('laravel-zoom.zoom_api_key'), config('laravel-zoom.zoom_api_secret'));
        });
    }

    public function boot(){
        $this->publishes([
            __DIR__ . '/../config/laravel-zoom.php', config_path('laravel-zoom.php')
        ]);
        if ($this->app->runningInConsole()){
            $this->commands([
                JWTCommand::class,
            ]);
        }
    }

}