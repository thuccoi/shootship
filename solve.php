<?php
include_once 'hardy.php';
include_once 'thor.php';
include_once 'kien.php';


function solve1($file = "data.txt", $player = 1) {
    return solveThor($file,$player);
}


function solve2($file = "data.txt", $player = 2){
    return solveKien($file,$player);
}

