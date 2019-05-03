<?php
namespace CodeZilla\LaravelZoom\Facades;

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
 * @method static generateSignature(int $meeting_number)
 * @method static getUsers(string $status = 'active', int $page_number = 1)
 * @method static getMeetings(string $user_id, string $type = 'live', int $page_number = 1)
 */

class LaravelZoom extends Facade{

    public static function getFacadeAccessor()
    {
        return 'laravelzoom';
    }

}