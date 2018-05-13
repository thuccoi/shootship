<?php

function solveSon($file = "data.txt", $player = 1, $size_map = 10) {
    $data = getData($file);
    $data_end = end($data);
    $square = [];
    $competitor_moves = [];
    $competitor_attack_fails = [];
    $my_attack_fails = [];
    $my_attack_on_targets = [];

    $me = $player;
    $competitor = ($player == 1) ? 2 : 1;

    for ($i = 0; $i <= $size_map * $size_map - 1; $i++) {
        array_push($square, $i);
    }

    foreach ($data as $key => $step) {
        if ($step[0] == $competitor && $step[1] == "D") {
            $competitor_moves[$key] = $step;
        }

        if ($step[0] == $competitor && $step[1] == "A" && $step[4] == "F") {
            $competitor_attack_fails[$key] = $step;
        }

        if ($step[0] == $me && $step[1] == "A" && $step[4] == "F") {
            $my_attack_fails[$key] = $step;
        }

        if ($step[0] == $me && $step[1] == "A" && $step[4] == "T") {
            $my_attack_on_targets[$key] = $step;
        }
    }

    if ($data_end[1] == 'D') {
        $random_target = array_rand($square);
        $x = floor($random_target / 10);
        $y = $random_target % 10;
        return "A {$x} {$y}";
    } else {
        $x_eliminate = $data_end[2];
        $y_eliminate = $data_end[3];
        if ($data_end[4] == 'T') {
            $center = array(4, 5);
            if (in_array($x_eliminate, $center) || in_array($y_eliminate, $center)) {
                $direction = ($x_eliminate > $y_eliminate) ? array('L', 'B') : array('R', 'T');
                $dir = array_rand($direction);
                return "D {$dir}";
            } else {
                $direction = ($x_eliminate > $y_eliminate) ? array('L', 'T') : array('R', 'B');
                $dir = array_rand($direction);
                return "D {$dir}";
            }
        } else {
            $random_target = array_rand($square);
            $x = floor($random_target / $size_map);
            $y = $random_target % $size_map;
            return "A {$x} {$y}";
        }
    }
}
