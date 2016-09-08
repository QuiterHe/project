<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-8
 * Time: 下午5:24
 * brief: 单例模式
 */

class DB {
    private $_handle = null;
    public function handle() {
        return $this->_handle;
    }

    private function __construct() {
        $this->_handle = rand(1, 100);
    }

    public static function get() {
        static $db = null;
        if($db == null) {
            $db = new DB();
        }
        return $db;
    }
}

var_dump('Handle:'.DB::get()->handle());
var_dump('Handle:'.DB::get()->handle());
var_dump('Handle:'.DB::get()->handle());
var_dump('Handle:'.DB::get()->handle());
var_dump('Handle:'.DB::get()->handle());