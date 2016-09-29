<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-29
 * Time: 上午11:55
 * url: https://www.codewars.com/kata/57591ef494aba64d14000526/train/php
 */


/**
    john(11) -->  [0, 0, 1, 2, 2, 3, 4, 4, 5, 6, 6]
    ann(6) -->  [1, 1, 2, 2, 3, 3]

    sum_john(75) -->  1720
    sum_ann(150) -->  6930
 */

function john($n) {
    $johnList = [0];
    $annList  = [1];
    for($i=1;$i<$n;$i++) {
        $t = $johnList[$i - 1];
        $johnList[] = $i - $annList[$t];
        $t = $annList[$i - 1];
        $annList[] = $i - $johnList[$t];
    }

    return $johnList;
}
function ann($n) {
    $johnList = [0];
    $annList  = [1];
    for($i=1;$i<$n;$i++) {
        $t = $johnList[$i - 1];
        $johnList[] = $i - $annList[$t];
        $t = $annList[$i - 1];
        $annList[] = $i - $johnList[$t];
    }

    return $annList;
}
function sumJohn($n) {
    $sum = john($n);
    return array_sum($sum);
}
function sumAnn($n) {
    $sum = ann($n);
    return array_sum($sum);
}

var_dump( john(11) );
var_dump( ann(6) );