<?php
namespace CodeZilla\LaravelZoom\Facade;

use Illuminate\Support\Facades\Facade;

/**
  * File : LaravelZoom.php
  * Author: Sainesh Mamgain
  * Email: saineshmamgain@gmail.com
  * Date: 29/4/19
  * Time: 4:46 PM
  */

/**
 * Class LaravelZoom
 * @package CodeZilla\LaravelZoom\Facade
 * @method static string generateSignature(int $meeting_number)
 */

class LaravelZoom extends Facade{

    public static function getFacadeAccessor()
    {
        return 'laravelzoom';
    }

}