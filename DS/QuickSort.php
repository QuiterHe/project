<?php
/**
 * Created by PhpStorm.
 * User: he
 * Date: 17-2-17
 * Time: 下午2:54
 */

// Best Case nlgn
// Worst Case n^2

function quickSort(array &$arr, $low, $high) {
    if( $low < $high ) {
        $pi = partition($arr, $low, $high);

        quickSort($arr, $low, $pi-1);
        quickSort($arr, $pi+1, $high);
    }
}

function partition(array &$arr, $low, $high) {
    $pivot = $arr[$high];

    $i = $low - 1;
    for($j=$low; $j<$high; $j++) {
        if( $arr[$j] <= $pivot ) {
            $i++;
            swapArr($arr, $i, $j);
        }
    }
    swapArr($arr, $i+1, $high);
    return $i + 1;

}

function swapArr(array &$arr, $a, $b) {
    $c = $arr[$a];
    $arr[$a] = $arr[$b];
    $arr[$b] = $c;
}


$arr = [10, 80, 30, 90, 40, 50, 70];
quickSort($arr, 0, 6);
var_dump($arr);
$arr2 = [10, 7, 8, 9, 1, 5];
quickSort($arr2, 0, 5);
var_dump($arr2);

