<?php
/**
 * @author hezhang
 * @date 2016/9/1
 * @time 17:34:15
 */

// require dirname(__DIR__).DIRECTORY_SEPARATOR.'bootstrap.php';


class MysqlUtil{
	private static $_sqlHandle = null;

	/*TODO  单例模式*/
	public static function getSqlHandle ($dbConfig) {
		if(self::$_sqlHandle)
			return self::$_sqlHandle;
		$handle = self::connectDB($dbConfig);
		if(-1 === $handle)
			return false;
		return $handle;
	}

	protected static function connectDB ($dbConfig) {
		$cparam = true;
		do{
			if(!is_array($dbConfig))
				$cparam = false;
			if(!isset($dbConfig['dbhost']))
				$cparam = false;
			if(!isset($dbConfig['dbuser']))
				$cparam = false;
			if(!isset($dbConfig['dbpass']))
				$cparam = false;
			if(!isset($dbConfig['dbname']))
				$cparam = false;
			if(!isset($dbConfig['dbport']))
				$cparam = false;
		} while(0);
		if(!$cparam)
			return -1;

		$handle = mysqli_connect($dbConfig['dbhost'], $dbConfig['dbuser'], $dbConfig['dbpass'], $dbConfig['dbname'], $dbConfig['dbport']);
		if ($handle === false)
			return -1;
		return $handle;
	}

	public static function query($handle, $sql) {
		$result = mysqli_query($handle, $sql);
		return $result === false ? false : $result;
	}

	#获取一条数据
	public static function getRow($handle, $sql) {
		if(!self::_checkHandle($handle))
			return false;
		$result = self::query($handle, $sql);
		if($result === false)
			return false;
		$row = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		return $row;
	}

	#获取多条数据
	public static function getList($handle, $sql) {
		if(!self::_checkHandle($handle))
			return false;
		$result = self::query($handle, $sql);
		if($result === false)
			return false;
		while($res = mysqli_fetch_assoc($result)) {
			$res[] = $res;
		}
		mysqli_free_result($result);
		return $res;
	}

	#获取一个数据
	public static function getOne($handle, $sql) {
		$row = self::getRow($handle, $sql);
		if(is_array($row))
			return current($row);
		return $row;
	}

	private static function _checkHandle($handle) {
		return is_object($handle) ? true : false;
	}


}

class RedisUtil{
	private static $_redisHandle = null;

	#获取redis handle
	public static function _getRedisHandle($dbConfig) {
		if(self::$_redisHandle)
			return self::$_redisHandle;
		$handle = self::connectDB($dbConfig);
		if(false === $handle)
			return false;
		return $handle;
	}

	#连接DB
	protected static function connectDB($dbConfig) {
		$param = true;
		do{
			if(!isset($dbConfig['host']))
				$param = false;
			if(!isset($dbConfig['port']))
				$param = false;
		}while(0);
		if (false === $param)
			return false;

		$redis = new Redis();
		$handle = $redis->connect($dbConfig['host'], $dbConfig['port']);
		if(false == $handle)
			return false;
		return $redis;
	}

	#设置字符串数据
	public static function setData($reids, $str) {
		$redis->set($str);
	}
	public static function createKey($str) {
		/* TODO */
	}
	public static function getData($handle, $key) {
		/* TODO */
	}


}

//$mysql  = new MysqlUtil;
//$config = [
//	'dbhost' => 'localhost',
//	'dbuser' => 'root',
//	'dbpass' => 'westos',
//	'dbname' => 'tq',
//	'dbport' => '3306',
//	];
//$handle = $mysql::getSqlHandle($config);
//$sql    = 'SELECT * FROM test';
//$result = $mysql::getRow($handle, $sql);
//var_dump($result);

//setcookie('user','hezhang');
//
//echo '<!DOCTYPE html>
//<html lang="en">
//<head>
//    <meta charset="UTF-8">
//    <title>CORS TEST</title>
//</head>
//<body>
//This is a CORS TEST RESTful Server!
//</body>
//</html>';


//$redis = new RedisUtil();
//$dbConfig = [
//	'host' => '127.0.0.1',
//	'port' => '6379'
//	];
//$redisHandle = $redis::_getRedisHandle($dbConfig);
//var_dump($redisHandle->set('name','hezhang'));
//var_dump($redisHandle->get('name'));


include "LoggerUtil.php";

$logUtil = new \C2b\Sdk\Util\AuctionLogUtil('bc');
$logUtil::logError('log test');

