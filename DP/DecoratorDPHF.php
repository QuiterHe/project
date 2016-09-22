<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-22
 * Time: 下午3:10
 * brief: HeadFirst中的装饰者模式实例
 */

/* 饮料和调料的超类 */
abstract class Beverage
{
    public $description = "Unknown Beverage";

    public function getDescription()
    {
        return $this->description;
    }

    public abstract function cost();
}

/* 装饰者(调料/Condiment) */
abstract class CondimentDecorator extends Beverage
{
    // 在PHP中，继承抽象类的抽象类不能重写父类的抽象方法
//    public abstract function getDescription();
}

/* 饮料1(Espresso) */
class Espresso extends Beverage
{
    public function __construct()
    {
        $this->description = 'Espresso';
    }

    public function cost()
    {
        return 1.99;
    }
}

/* 饮料2(HouseBlend) */
class HouseBlend extends Beverage
{
    public function __construct()
    {
        $this->description = "HouseBlend";
    }

    public function cost()
    {
        return .89;
    }
}

/* 调料1(Mocha) */
class Mocha extends CondimentDecorator
{
    protected $beverage;

    public function __construct(Beverage $beverage)
    {
        $this->beverage = $beverage;
    }

    public function getDescription()
    {
        return $this->beverage->getDescription() . ", Mocha";
    }

    public function cost()
    {
        return $this->beverage->cost() + .20;
    }
}

/* 调料2(Soy) */
class Soy extends CondimentDecorator
{
    protected $beverage;

    public function __construct(Beverage $beverage)
    {
        $this->beverage = $beverage;
    }

    public function getDescription()
    {
        return $this->beverage->getDescription() . ", Soy";
    }

    public function cost()
    {
        return $this->beverage->cost() + .10;
    }
}

// TEST
$beverage = new Espresso();
echo 'Description: ' . $beverage->getDescription() . "\n" . "Cost: " . $beverage->cost() . "\n";
/* TODO 利用工厂模式或者生成器模式建立被装饰对象(饮料) */
$beverage2 = new Mocha($beverage);
$beverage2 = new Mocha($beverage2);
$beverage2 = new Soy($beverage2);
echo 'Description: ' . $beverage2->getDescription() . "\n" . "Cost: " . $beverage2->cost() . "\n";