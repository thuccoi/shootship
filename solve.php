<?php

include_once 'hardy.php';
include_once 'thor.php';
include_once 'kien.php';
include_once 'phuong.php';
include_once 'son.php';
include_once 'dat.php';


$speed = 20;
if (isset($_GET['speed'])) {
    $speed = $_GET['speed'];
}

$sizemap = 20;
$file = "data.txt";


function solve1($file = "data.txt", $player = 1, $sizemap = 10) {
    //   return solveThor($file, $player, $sizemap);
    return solveKien($file, $player, $sizemap);
//     return solveDat($file, $player, $sizemap);
//     return solvePhuong($file, $player, $sizemap);
//       return solveSon($file, $player, $sizemap);
//    return solveHardy($file, $player, $sizemap);
}

function solve2($file = "data.txt", $player = 2, $sizemap = 10) {
     return solveThor($file, $player, $sizemap);
//     return solveSon($file, $player, $sizemap);
//    return solvePhuong($file, $player, $sizemap);
//    return solveKien($file, $player, $sizemap);
//     return solveHardy($file, $player, $sizemap);
    //   return solveDat($file, $player, $sizemap);
}
