<?php

function solve1($file = "data.txt", $player = 1) {
    $r = rand(0, 123);
    if ($r % 2 == 1) {
        $x = rand(0, 10);
        $y = rand(0, 10);
        return "A {$x} {$y}";
    } else {
        $dir = 'T';
        $r = rand(0, 4);
        if ($r == 0) {
            $dir = 'T';
        } elseif ($r == 1) {
            $dir = 'L';
        } elseif ($r == 2) {
            $dir = 'B';
        } elseif ($r == 3) {
            $dir = 'R';
        }
        return "D {$dir}";
    }
}



function solve2($file = "data.txt", $player = 2) {
    $r = rand(0, 123);
    if ($r % 2 == 1) {
        $x = rand(0, 10);
        $y = rand(0, 10);
        return "A {$x} {$y}";
    } else {
        $dir = 'T';
        $r = rand(0, 4);
        if ($r == 0) {
            $dir = 'T';
        } elseif ($r == 1) {
            $dir = 'L';
        } elseif ($r == 2) {
            $dir = 'B';
        } elseif ($r == 3) {
            $dir = 'R';
        }
        return "D {$dir}";
    }
}
