<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-10-12
 * Time: 下午3:37
 * brief: 工厂模式(封装new)
 */

interface IUser {
    function getName();
}

class User implements IUser {
    protected $id = null;

    public function __construct( $id ) {
        $this->id = $id;
    }

    public function getName() {
        return $this->id == 0 ? 'root' : 'other';
    }
}

class UserFactory {
    public static function create( $id ) {
        return new User( $id );
    }
}

class UserLogin {
    protected  $user = null;

    public function __construct( $id ) {
        $this->user = UserFactory::create( $id );
    }

    public function login() {
        echo $this->user -> getName();
        /* TODO */
    }
}

$login = new UserLogin( 0 );
$login->login();


/* 简单工厂的变形 */
class User2 implements IUser {
    protected $id = null;

    public function __construct( $id ) {
        $this->id = $id;
    }

    public function getName() {
        return $this -> id == 0 ? 'root' : 'other';
    }

    public static function create( $id ) {
        return new self( $id );
    }

}

$uo = User2::create(0);
echo $uo->getName();

