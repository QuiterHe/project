<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-13
 * Time: 下午2:46
 * brief: 策略模式
 */

/* Case 1: 用户查找策略 */
interface IStrategy
{
    public function filter( $filter );
}

/* 用户查找算法簇： 查找名称在$name之后的用户 */
class FindAfterStrategy implements IStrategy
{
    private $_name ;

    public function __construct( $name ) {
        $this->_name = $name;
    }

    public function filter( $record ) {
        return strcmp( $this->_name, $record ) <= 0 ;
    }
}

/* 用户查找算法簇： 50%随机查找算法 */
class RandomStrategy implements IStrategy
{
    public function filter( $record ) {
        return rand(0, 1) > 0.5;
    }
}

/* 算法簇调用者 */
class UserList
{
    private $_list = [];

    public function __construct( $names ) {
        if( $names != null ) {
            foreach( $names as $name ) {
                $this->_list[] = $name;
            }
        }
    }

    public function add( $name ) {
        if( $name ) {
            $this->_list[] = $name;
        }
    }

    /* 即插即用的查找算法 */
    public function find( IStrategy $filter ) {
        $res = [];
        foreach( $this->_list as $user ) {
            if( $filter->filter( $user ) ) {
                $res[] = $user;
            }
        }

        return $res;
    }
}

$user = new UserList(["Andy", "Jack", "Lori", "Megan"]);
$res1 = $user->find( new FindAfterStrategy( "J" ) );
print_r( $res1 );

$res2 = $user->find( new RandomStrategy() );
print_r( $res2 );
