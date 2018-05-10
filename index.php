<?php
include 'war.php';


$game = new Game(3);

if (!loadConfig($game)) {
    echo 'Cấu hình sai';
    exit;
}

war($game);

stats($game);
