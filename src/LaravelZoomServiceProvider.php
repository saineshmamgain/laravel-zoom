<?php
namespace CodeZilla\LaravelZoom;
use Illuminate\Support\ServiceProvider;

/**
  * File : LaravelZoomServiceProvider.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 4:42 PM
  */


class LaravelZoomServiceProvider extends ServiceProvider {

    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-zoom.php', 'laravel-zoom'
        );
        $this->app->bind('laravelzoom', function (){
            $class = config('laravel-zoom.classes.laravel_zoom');
            return new $class(config('laravel-zoom.zoom_api_key'), config('laravel-zoom.zoom_api_secret'));
        });
    }

    /**
     *
     */
    public function boot(){
        $this->publishes([
            __DIR__ . '/../config/laravel-zoom.php', config_path('laravel-zoom.php')
        ]);
        if ($this->app->runningInConsole()){
            $this->commands([
                config('laravel-zoom.classes.jwt_command'),
            ]);
        }
    }

}