<?php
namespace CodeZilla\LaravelZoom;

use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * File : JWT.php
 * Author: Sainesh Mamgain
 * Email: saineshmamgain@gmail.com
 * Date: 2/5/19
 * Time: 4:46 PM
 */


class JWT {

    public static function generate(string $key, string $secret, array $headers, array $payload, string $algo = "sha256"){
        if (self::validJWTExists()){
            return env('ZOOM_JWT_TOKEN');
        }
        $headerEncoded = self::base64UrlEncode(json_encode($headers));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        $data = "$headerEncoded.$payloadEncoded";
        $signature = hash_hmac($algo, $data, $secret,true);
        $signature = self::base64UrlEncode($signature);
        $jwt = "$data.$signature";
        self::updateEnv([
            'ZOOM_API_KEY' => $key,
            'ZOOM_API_SECRET' => $secret,
            'ZOOM_JWT_TOKEN' => $jwt,
            'ZOOM_JWT_EXPIRES_ON' => $payload['exp']
        ]);
        return $jwt;
    }

    public static function validJWTExists(){
        if (!empty(env('ZOOM_JWT_TOKEN')) && !empty(env('ZOOM_JWT_EXPIRES_ON'))){
            $expires = Carbon::createFromTimestamp(env('ZOOM_JWT_EXPIRES_ON'));
            if ($expires->isFuture()){
                return true;
            }
        }
        return false;
    }

    public static function base64UrlEncode(string $data, bool $usePadding = false): string
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');
        return true === $usePadding ? $encoded : rtrim($encoded, '=');
    }

    public static function base64UrlDecode(string $data): string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid data provided');
        }
        return $decoded;
    }

    public static function regenerate(){

    }

    public static function updateEnv($data = array()){
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