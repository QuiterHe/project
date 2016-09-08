<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-8
 * Time: 上午11:28
 * brief: logger类
 */

/* 配置文件 */
//namespace C2b\Sdk\Vars;
namespace C2b\Sdk\Vars {
    class LogVars
    {
        const LOG_DIR = '/home/he/test/www/service_logs/services/';
        const LOG_PREFIX = 'c2b.';
    }
}

/* log基类 */
//namespace Pub\Log;
namespace Pub\Log {
    class Logger
    {

        const DEBUG = 1; // Most Verbose
        const INFO = 2; // ...
        const WARN = 3; // ...
        const ERROR = 4; // ...
        const FATAL = 5; // Least Verbose

        private $saveDir;
        private $category;

        public function __construct($saveDir, $category)
        {
            $this->saveDir = $saveDir;
            $this->category = $category;
        }

        /**
         * 记录一条Warning日志
         * @param[in] string message 日志的正文
         */
        public function logWarn($message)
        {
            $this->formatMessage($message);
            $message .= " " . self:: formatExtInfo();
            $this->log($message, self::WARN);
        }

        /**
         * 记录一条Error日志
         * @param[in] string message 日志的正文
         */
        public function logError($message)
        {
            $this->formatMessage($message);
            $message .= " " . self:: formatExtInfo();
            $this->log($message, self::ERROR);
        }

        /**
         * 记录一条Fatal日志
         * @param[in] string message 日志的正文
         */
        public function logFatal($message)
        {
            $this->formatMessage($message);
            $message .= " " . self:: formatExtInfo();
            $this->log($message, self::FATAL);
        }

        /**
         * 记录一条Info日志
         * @param[in] string message 日志的正文
         */
        public function logInfo($message)
        {
            $this->formatMessage($message);
            $this->log($message . " @[php@]", self::INFO);
        }

        /**
         * 记录一条Debug日志
         * @param[in] string message 日志的正文
         */
        public function logDebug($message)
        {
            $this->formatMessage($message);
            $this->log($message . " @[php@]", self::DEBUG);
        }

        /**
         * 实现日志输出的内部函数
         * 根据配置的不同，日志可以送给本机的Scribed服务，也可以送到临时目录下
         * @param[in] string messsage 日志的正文
         * @param[in] enum priority 日志输出的级别
         * @param[in] string category 日志的类别，格式 {产品线}.{模块1}.{子模块1}...
         */
        private function log($message, $priority)
        {
            if (!is_dir($this->saveDir)) {
                mkdir($this->saveDir);
            }

            $logFileName = $this->saveDir;
            if (substr($logFileName, -1) != '/') {
                $logFileName .= '/';
            }

            $logFileName .= $this->category . '.log_' . date('Y-m-d');
            @file_put_contents($logFileName, date("Y-m-d H:i:s", time()) . " [$priority] $message\n", FILE_APPEND);
        }

        /**
         * @breif 格式化截取信息长度
         * @param $message
         * @param $maxLength 默认3k
         */
        private function formatMessage(&$message, $maxLength = 3000)
        {
            // 确保信息是string
            if (!is_string($message)) {
                $message = var_export($message, true);
            }
            // 超过3k做消息内容的截取
            if (strlen($message) < $maxLength) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE); //防止换行
                return;
            }
            $message = json_encode(substr($message, 0, $maxLength), JSON_UNESCAPED_UNICODE);
        }

        private function formatExtInfo()
        {
            // 在 5.3.6 之前，仅仅能使用的值是 TRUE 或者 FALSE，分别等于是否设置 DEBUG_BACKTRACE_PROVIDE_OBJECT 选项。
            $stacks = debug_backtrace(false);
            foreach ($stacks as $key => &$stack) {
                unset($stack['args']);
            }
            $stack_clean = array_slice($stacks, 2); // remove myself and logXXX
            $request = $_SERVER;
            unset($request['HTTP_COOKIE']);
            $info = array("callstack" => $stack_clean, //$stack_clean,
                "request" => $request);
            return "@[php" . @json_encode($info) . "@]";
        }

    }
}

/* Log类库 */
//namespace C2b\Library\Util;
namespace C2b\Library\Util {
    class LogUtil
    {
        //默认日志路径
        const DIR_DEFAULT = '/data/service_logs/services/';
        //默认日志文件前缀
        const CATE_DEFAULT = 'c2b.web';
        //运行时路径
        private $_DIR = self::DIR_DEFAULT;
        //运行时前缀
        private $_CATE = self::CATE_DEFAULT;
        //运行时logger实例
        private $_LOGGER = null;
        //static logger
        private static $_INSTANCE = null;

        /**
         * 返回自身实例
         * @return LogUtil
         */
        public static function getInstance()
        {
            if (self::$_INSTANCE instanceof self) {
                return self::$_INSTANCE;
            }
            self::$_INSTANCE = new self();
            return self::$_INSTANCE;
        }

        /**
         * 设置日志路径与名称格式
         * @param string $dir 日志保存路径
         * @param string $cate 日志文件前缀
         */
        public function __construct($dir = self::DIR_DEFAULT, $cate = self::CATE_DEFAULT)
        {
            $this->_CATE = $cate;
            $this->_DIR = $dir;
            $this->_init();
        }

        /**
         * 初始化外部log类库
         */
        private function _init()
        {
            if (!isset($this->_LOGGER) || !($this->_LOGGER instanceof \Pub\Log\Logger)) {
                $this->_LOGGER = new \Pub\Log\Logger(
                    $this->_DIR, $this->_CATE
                );
            }
        }

        /**
         * 记录一条Warning日志
         * @param[in] string message 日志的正文
         */
        public function logWarn($message)
        {
            return $this->_LOGGER->logWarn($message);
        }

        /**
         * 记录一条Error日志
         * @param[in] string message 日志的正文
         */
        public function logError($message)
        {
            return $this->_LOGGER->logError($message);
        }

        /**
         * 记录一条Fatal日志
         * @param[in] string message 日志的正文
         */
        public function logFatal($message)
        {
            return $this->_LOGGER->logFatal($message);
        }

        /**
         * 记录一条Info日志
         * @param[in] string message 日志的正文
         */
        public function logInfo($message)
        {
            return $this->_LOGGER->logInfo($message);
        }

        /**
         * 记录一条Debug日志
         * @param[in] string message 日志的正文
         */
        public function logDebug($message)
        {
            return $this->_LOGGER->logDebug($message);
        }
    }
}

/* log API */
//namespace C2b\Sdk\Util;
namespace C2b\Sdk\Util {
    class AuctionLogUtil
    {
        const DEBUG = 1; // Most Verbose
        const INFO = 2; // ...
        const WARN = 3; // ...
        const ERROR = 4; // ...
        const FATAL = 5; // Least Verbose

        private static $_CHANNEL = 'web';
        private static $_LOG_UTIL = null;

        private static $_LOG_LEVEL = array(
            self::DEBUG => 'debug',
            self::INFO => 'info',
            self::WARN => 'warn',
            self::ERROR => 'error',
            self::FATAL => 'fatal',
        );

        const LOG_CATEGORY = 'vehicle_auction';

        public static function __callStatic($method, $params)
        {
            $method = '_' . $method;
            if (!method_exists(__CLASS__, $method) || ('_checkLogger' == $method) || empty(current($params))) {
                return false;
            }
            self::_checkLogger();
            return call_user_func_array('self::' . $method, $params);
        }

        private static function _checkLogger()
        {
            if (!isset(self::$_LOG_UTIL) || !(self::$_LOG_UTIL instanceof \C2b\Library\Util\LogUtil)) {
                self::$_LOG_UTIL = new \C2b\Library\Util\LogUtil(
                    \C2b\Sdk\Vars\LogVars::LOG_DIR, \C2b\Sdk\Vars\LogVars::LOG_PREFIX . self::$_CHANNEL
                );
            }
        }

        public function __construct($channel = 'web')
        {
            if (!is_callable($channel, true)) {
                self::$_CHANNEL = 'web';
            } else {
                self::$_CHANNEL = trim($channel);
            }
        }

        public static function log($level, $msg)
        {
            if (!array_key_exists($level, self::$_LOG_LEVEL) || empty($msg)) {
                return false;
            }
            self::_checkLogger();
            $method = 'log' . ucfirst(self::$_LOG_LEVEL[$level]);
            return self::$method($msg);
        }

        private static function _logDebug($msg)
        {
            return self::$_LOG_UTIL->logDebug($msg, self::LOG_CATEGORY);
        }

        private static function _logInfo($msg)
        {
            return self::$_LOG_UTIL->logInfo($msg, self::LOG_CATEGORY);
        }

        private static function _logWarn($msg)
        {
            return self::$_LOG_UTIL->logWarn($msg, self::LOG_CATEGORY);
        }

        private static function _logError($msg)
        {
            return self::$_LOG_UTIL->logError($msg, self::LOG_CATEGORY);
        }

        private static function _logFatal($msg)
        {
            return self::$_LOG_UTIL->logFatal($msg, self::LOG_CATEGORY);
        }
    }
}