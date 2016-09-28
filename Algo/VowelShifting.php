<?php
/**
 * Created by PhpStorm.
 * User: hezhang
 * Date: 16-9-28
 * Time: 上午11:06
 */

function vowelShift($text, $n) {
    if( ($text == null) || empty($text) ) {
        return $text;
    }

    $vowels = ['a', 'e', 'i', 'o', 'u'];
    $strlen = strlen($text);
    $vowelsPos = [];
    for( $i = 0;$i < $strlen;$i++ ) {
        if( in_array($text[$i], $vowels) )
            $vowelsPos[$i] = $text[$i];
    }

    $vowelsArr = array_values($vowelsPos);
    $count     = count($vowelsArr);
    $n         = $n % $count;
    if($n >= 0) {
        $tmp = array_splice($vowelsArr, $count-$n );
        $vowelsArr = array_merge($tmp, $vowelsArr);
    }else {
        $tmp = array_splice($vowelsArr, 0, -$n);
        $vowelsArr = array_merge($vowelsArr, $tmp);
    }
    $j = 0;
    foreach($vowelsPos as $key => $val) {
        $text[$key] = $vowelsArr[$j];
        $j++;
    }

    return $text;
}

var_dump(vowelShift("This is a test!", -1));var_dump(vowelShift("This is a test!", -1));