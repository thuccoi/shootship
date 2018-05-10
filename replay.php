<?php

include 'war.php';

$time = $_GET['time'];
if ($time > 0) {
    $game = new Game(3);

    if (!loadConfig($game, "history/config_{$time}.txt")) {
        echo 'Cấu hình sai';
        exit;
    }


    $maps = replay($game, "history/data_{$time}.txt");

    stats($game);

    foreach ($maps as $map) {
        echo $map;
    }
}