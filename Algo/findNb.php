<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-28
 * Time: 下午2:59
 * url: https://www.codewars.com/kata/build-a-pile-of-cubes/train/php
 */

// 使用二分法，time out($m=4183059834009)
function mid($s, $e, $m) {
    if( $s < $e ) {
//        $mid = intval( floor( ($s + $e) / 2 ) ); // 二分法
        $mid = rand($s, $e);    // 二分法改进版本--随机生成数（仍然会超时）
        $res = 0;
        $mids = $mid;
        while($mids != 0) {
            $res += $mids * $mids * $mids;
            $mids--;
        }
        if($res == $m) {
            return $mid;
        }else{
            return $res > $m ? mid($s, $mid, $m) : mid($mid, $e, $m);
        }
    }else if($s == $e) {
        return $m == $s * $s * $s ? $s : -1;
    }
}

// 空间换时间
function _nbArr($num) {
    $res = array_fill(0, $num+1, 0);
    for($i=1;$i<=$num;$i++) {
        $res[$i] += $i * $i * $i + $res[$i-1];
    }

    return $res;
}

function findNb($m) {
    $start = 0;
    $end   = $m;
    return mid($start, $end, $m);
}

function findNb2($m) {
    $res = _nbArr(5000);
    $key = array_search($m, $res);
    return $key ? $key : -1;
}
//var_dump(findNb(9));
//var_dump(findNb(4*4*4+3*3*3+2*2*2+1));
//var_dump(findNb(1071225));
//var_dump(findNb2(4183059834009));

/* 当使用空间换时间"巧妙的"AC后才发现自己走远了～～ */
/* 一开始的思想是将问题转化为函数求解问题，遂采用二分法。time out后考虑用牛顿法0.0 */
/* 后来才发现只需要逆向思维即可 */

function findNb3($m) {
    $res = 0;
    for($i=1;$res<$m;$i++) {
        $res += pow($i, 3);
    }
    return $res == $m ? $i -1 : -1;
}

var_dump(findNb3(4183059834009));