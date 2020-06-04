<?php

// 斐波那契数列递归优化

function fun($n)
{

    if ($n < 3) {
        if ($n === 1) {
            return [0, 1];
        }
        if ($n === 2) {
            return [1, 1];
        }
    }

    $a = fun($n - 1);
    return [$a[1], $a[0] + $a[1]];
}

$stime = microtime(true);

echo fun(40)[1];

$etime = microtime(true);
$total = $etime - $stime;
echo "<br />当前页面执行时间为：{$total} 秒";

