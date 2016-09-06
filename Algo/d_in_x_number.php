<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-6
 * Time: 下午3:23
 * url:https://www.codewars.com/kata/numbers-with-this-digit-inside/train/php
 */

function numbersWithDigitInside($x, $d) {
    $start = 1;
    $end   = $x;
    $res   = [];
    for($curr=$start;$curr<=$end;$curr++){
        $t = $curr;
        while(1){
            if($t<10){
                if($t == $d) {
                    $res[] = $curr;
                    break;
                }
                break;
            }else{
                $y = $t % 10;
                if($y == $d){
                    $res[] = $curr;
                    break;
                }else{
                    $t /= 10;
                    $t = intval($t);
                }
            }
        };
    }
    $count = count($res);
    $sum   = array_sum($res);
    var_dump($res);
    $product = $res ? bigProduct($res) : 0;
    $res =  [$count, $sum, $product];
    return $res;
}

// 数组连乘（大数）
function bigProduct($arr){
    $res = 1;
    foreach($arr as $val) {
        $res = multiply( str_split($res), str_split($val) );
    }
    return $res;
}

//大数相乘最直接的算法就是模拟小学学到的竖式乘法，可以使用数组或者字符串来存储乘数和被乘数
function multiply($str1,$str2){
    $str1len = count($str1);
    $str2len = count($str2);

    $str3 = array();
    //初始化结果数组
    for($i=0;$i<$str1len+$str2len;$i++){
        $str3[$i]=0;
    }

    //计算交叉相乘的结果
    for($i=0;$i<$str1len;$i++){
        for($j=0;$j<$str2len;$j++){
            $str3[$i+$j+1]+=$str1[$i]*$str2[$j];
        }
    }

    //如果大于10，则进位
    for($i=$str1len+$str2len-1;$i>=0;$i--){
        if($str3[$i]>=10){
            $str3[$i-1]+=intval($str3[$i]/10);
            $str3[$i]%=10;
        }

    }

    //去掉高位的0
    $i=0;
    while($str3[$i]==0){
        $i++;
    }

    $str4 = array();

    //复制到新的数组
    for($j=0;$i<$str1len+$str2len;$i++,$j++){
        $str4[$j]=$str3[$i];
    }


    //输出
//    foreach($str4 as $tmp){
//        echo $tmp;
//    }

    return implode("", $str4);

}


// 大数相加
function bigNumberAdd($str1, $str2) {
    $lenstr1 = strlen($str1);
    $lenstr2 = strlen($str2);
    $res     = [];

    // 对齐位数
    if($lenstr1 > $lenstr2) {
        $str2 = str_pad($str2, $lenstr1, 0, STR_PAD_LEFT);
    }else{
        $str1 = str_pad($str1, $lenstr2, 0, STR_PAD_LEFT);
    }
    // 初始结果数组
    $res = array_fill(0, strlen($str2), 0);

    // 按位相加
    for($i=strlen($str1)-1;$i>=0;$i--){
        $tmp = $str1[$i] + $str2[$i];
        $res[$i] += $tmp;
        if($res[$i]>=10) {
            $res[$i] -= 10;
            $res[$i] += 1;
        }
    }

    return implode("", $res);
}

var_dump(bigNumberAdd('2036465424168354343213213213213213324654654','65465786132418435413543565416543216513132132132165135135135135135135135135135'));
// 大数相加也可以使用BC数学函数
var_dump(bcadd('2036465424168354343213213213213213324654654','65465786132418435413543565416543216513132132132165135135135135135135135135135'));
var_dump(65465786132418435413543565416543216513132132132165135135135135135135135135135 + 2036465424168354343213213213213213324654654);
//var_dump(numbersWithDigitInside(1000, 0));
//print_r(str_split(1654145));