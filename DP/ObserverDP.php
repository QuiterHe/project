<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-8
 * Time: 下午5:59
 * brief: 观察者模式
 */

interface IObserver
{
    function onChanged( $sender, $args );
}

interface IObservable
{
    function addObserver( $observer );
}

class UserList implements IObservable {
    private $_observer = [];

    public function addObserver( $observer ) {
            $this->_observer[] = $observer;
    }

    public function addCustomer( $name ) {
        foreach( $this->_observer as $obs ) {
            $obs->onChanged( $this, $name );
        }
    }
}

class UserListLogger implements IObserver {
    public function onChanged( $sender, $args ) {
        echo "$args added to list\n";
    }
}

$ul = new UserList();
$ul->addObserver(new UserListLogger());
$ul->addCustomer('hezhang');

/*====================================================================================================================*/
/* 被观察对象 */
class UserList2 implements IObservable {
    private $_observer = [];

    public function addObserver( $observer ) {
        $this->_observer[] = $observer;
    }

    public function addCustomer( $name ) {
        foreach( $this->_observer as $obs ) {
            $obs->onChanged($this, $name);
        }
    }
}

/* 观察对象 */
class UserListLogger2 {
    public function onChanged( $sender, $args ) {
        echo "$args added to list\n";
    }
}

echo "=========================="."\n";
$ul = new UserList2();
/* 注册 */
$ul->addObserver( new UserListLogger2() );
/* 回调 */
$ul->addCustomer( 'wanghao' );