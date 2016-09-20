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

    private static $_object = null;

    /* 一旦气象测量数据更新，此方法会被调用 */
    public function measurementsChanged()
    {
        self::$_object = self::$_object ? self::$_object : new WeatherObservers();
        $temp     = $this->getTemperature();
        $humidity = $this->getHumidity();
        $pressure = $this->getPressure();
        self::$_object->action($temp, $humidity, $pressure);
    }

    /* 添加天气面板 */
    public function addWeatherDisplay( $observer )
    {
        self::$_object = self::$_object ? self::$_object : new WeatherObservers();
        self::$_object->addObserver( $observer );
    }
}

/* 天气数据展示接口 */
interface WeatherDisplay
{
    public function onChange($temp, $humidity, $pressure);
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
    public function action($temp, $humidity, $pressure)
    {
        foreach( $this->_observer as $obs )
        {
            $obs->onChange($temp, $humidity, $pressure);
        }
    }
}

/* 当前天气状况 */
class currentWeatherDisplay implements WeatherDisplay
{
    /* 回调方法 */
    public function onChange($temp, $humidity, $pressure)
    {
        $this->update($temp, $humidity, $pressure);
    }

    /* 当前天气状况实现 */
    public function update($temp, $humidity, $pressure)
    {
        echo "Temperature: $temp \nHumidity: $humidity\nPressure: $pressure\n";
    }
}

$weather = new WeatherData();
$weather->addWeatherDisplay( new currentWeatherDisplay() );
$weather->measurementsChanged();