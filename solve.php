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

$sizemap = 30;
if (isset($_GET['sizemap'])) {
    $sizemap = $_GET['sizemap'];
}

$filedata = "data.txt";
$fileconfig1 = "config1.txt";
$fileconfig2 = "config2.txt";

$uplay1 = 'thor';
$uplay2 = 'thor';
if (isset($_GET['ship1'])) {
    $uplay1 = $_GET['ship1'];
}
if (isset($_GET['ship2'])) {
    $uplay2 = $_GET['ship2'];
}

function solve1($filedata = "data.txt", $player = 1, $sizemap = 10, $uplay = 'thor') {
    switch ($uplay) {
        case 'thor':
            return solveThor($filedata, $player, $sizemap);
            break;
        case 'hardy':
            return solveHardy($filedata, $player, $sizemap);
            break;
        case 'son':
            return solveSon($filedata, $player, $sizemap);
            break;
        case 'phuong':
            return solvePhuong($filedata, $player, $sizemap);
            break;
        case 'dat':
            return solveDat($filedata, $player, $sizemap);
            break;
        case 'kien':
            return solveKien($filedata, $player, $sizemap);
            break;
    }
}

function solve2($filedata = "data.txt", $player = 2, $sizemap = 10, $uplay = 'thor') {
    switch ($uplay) {
        case 'thor':
            return solveThor($filedata, $player, $sizemap);
            break;
        case 'hardy':
            return solveHardy($filedata, $player, $sizemap);
            break;
        case 'son':
            return solveSon($filedata, $player, $sizemap);
            break;
        case 'phuong':
            return solvePhuong($filedata, $player, $sizemap);
            break;
        case 'dat':
            return solveDat($filedata, $player, $sizemap);
            break;
        case 'kien':
            return solveKien($filedata, $player, $sizemap);
            break;
    }
}
