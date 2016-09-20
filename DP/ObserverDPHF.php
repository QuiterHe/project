<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-20
 * Time: 上午10:45
 * brief: HeadFirst中的观察者实例
 */

class WeatherData
{
    /* 被观察者对象 */
    private static $_object = null;

    /* 构造函数： (为了)依赖反转 */
    public function __construct(WeatherObserver $observable)
    {
        self::$_object = self::$_object ? self::$_object : new $observable;
    }

    /* 获取最近的温度测量数据 */
    public function getTemperature()
    {
        return rand(0, 50);
    }

    /* 获取最近的湿度测量数据 */
    public function getHumidity()
    {
        return rand(0, 20);
    }

    /* 获取最近的气压测量数据 */
    public function getPressure()
    {
        return rand(50, 100);
    }

    /* 一旦气象测量数据更新，此方法会被调用 */
    public function measurementsChanged()
    {
        $args['temp']     = $this->getTemperature();
        $args['humidity'] = $this->getHumidity();
        $args['pressure'] = $this->getPressure();
        self::$_object->action( $args );
    }

    /* 添加天气面板 */
    public function addWeatherDisplay( $observer )
    {
        self::$_object->addObserver( $observer );
    }
}

/* 天气数据展示接口 */
interface WeatherDisplay
{
    public function onChange( $sender, $args );
}

/* 天气观察者接口 */
interface WeatherObserver
{
    public function addObserver( $observer );
}

/* 天气观察者 */
class WeatherObservers implements WeatherObserver
{
    private $_observer = [];

    /* 添加观察者 */
    public function addObserver( $observer )
    {
        $this->_observer[] = $observer;
    }

    /* 变化发生时调用此函数 */
    public function action( $args )
    {
        foreach( $this->_observer as $obs )
        {
            $obs->onChange( $this, $args );
        }
    }
}

/* 当前天气状况 */
class currentWeatherDisplay implements WeatherDisplay
{
    /* 自我注册 */
    public function addObserver( $observable )
    {
        $observers = new $observable;
        $observers->addObserver($this);
    }

    /* 回调方法 */
    public function onChange( $sender, $args )
    {
        $this->update( $sender, $args );
    }

    /* 当前天气状况实现 */
    public function update( $sender, $args )
    {
        $temp = $args['temp'];
        $humidity = $args['humidity'];
        $pressure = $args['pressure'];
        echo "Temperature: $temp\nHumidity: $humidity\nPressure: $pressure\n";
    }
}

$weather = new WeatherData( new WeatherObservers() );
$weather->addWeatherDisplay( new currentWeatherDisplay() );
$weather->measurementsChanged();