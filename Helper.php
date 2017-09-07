<?php

function toBite($num){
    $format = ['KiB' => 1024, 'MiB' => 1048576];
    $info = explode(' ', trim($num));
    if (isset($format[$info[1]])) {
        return intval($format[$info[1]] * $info[0]);
    }
    return 0;
}