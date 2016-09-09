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


/* 对象的构建只能通过 #静态方法# 来调用，此静态方法返回一个 #静态自身实例# */
class DB2 {
    private $_handle = null;

    public function handle() {
        return $this->_handle;
    }

    /* 封闭new，禁止构造新对象 */
    private function __construct() {
        $this->_handle = rand(1, 100);
    }

    /* 开放public static 方法，返回一个static self实例 */
    public static function get() {
        static $db = null;
        return $db instanceof self ? $db : ($db = new self);
    }
}



echo "======================================"."\n";
var_dump('Handle:'.DB2::get()->handle());
var_dump('Handle:'.DB2::get()->handle());
var_dump('Handle:'.DB2::get()->handle());
var_dump('Handle:'.DB2::get()->handle());
var_dump('Handle:'.DB2::get()->handle());