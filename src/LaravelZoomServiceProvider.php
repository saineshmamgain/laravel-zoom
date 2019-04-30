<?php
namespace CodeZilla\LaravelZoom;
use Illuminate\Support\ServiceProvider;

/**
  * File : LaravelZoomServiceProvider.php
  * Author: Sainesh Mamgain
  * Email: sainesh.m@basicfirst.net
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
    }

}