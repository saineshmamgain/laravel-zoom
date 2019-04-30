<?php
namespace CodeZilla\LaravelZoom;

/**
  * File : LaravelZoom.php
  * Author: Sainesh Mamgain
  * Email: sainesh.m@basicfirst.net
  * Date: 29/4/19
  * Time: 4:47 PM
  */


class LaravelZoom {

    private $key;
    private $secret;

    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function generateSignature($meeting_number, $role = 0){
        $time = time() * 1000;
        $data = base64_encode($this->key . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $this->secret, true);
        $_sig = $this->key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

}