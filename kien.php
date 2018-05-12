<?php

function solveKien($file = "data.txt", $player, $size_map) {

    $me = $player;
    $enemy = $player == 1 ? 2 : 1;

    $data = getData($file);

    $my_attack_fails = [];
    $my_attack_hits = [];
    $enemy_moves = [];
    $hit_targets = [];
    $available_targets = [];

    for ($i = 0; $i <= ($size_map * $size_map - 1); $i++) {
        array_push($available_targets, $i);
    }

    foreach ($data as $k => $v) {
        if($v[0] == $me && $v[1] == "A" && $v[4] == "F"){
            $my_attack_fails[$k] = $v;
        }

        if($v[0] == $me && $v[1] == "A" && $v[4] == "T"){
            $my_attack_hits[$k] = $v;
        }

        if($v[0] == $enemy && $v[1] == "D"){
            $enemy_moves[$k] = $v;
        }
    }

    if(count($enemy_moves)){
        foreach ($my_attack_fails as $k1 => $v1) {
            foreach ($enemy_moves as $k2 => $v2) {
                if($k1 < $k2){
                    if($v2[2] == "L"){
                        $v1[3] = $v1[3] - 1;
                    }
                    if($v2[2] == "R"){
                        $v1[3] = $v1[3] + 1;
                    }
                    if($v2[2] == "T"){
                        $v1[2] = $v1[2] - 1;
                    }
                    if($v2[2] == "B"){
                        $v1[2] = $v1[2] + 1;
                    } 
                }
            }
            $index = $v1[2] * $size_map + $v1[3];
            if(in_array($index, $available_targets)){
                unset($available_targets[$index]);
            }
        }
    }
    

    if(count($my_attack_hits)){
        $my_attack_hit = [];
        foreach ($my_attack_hits as $k1 => $v1) {
            if(count($enemy_moves)){
                foreach ($enemy_moves as $k2 => $v2) {
                    if($k1 < $k2){
                        if($v2[2] == "L"){
                            $v1[3] = $v1[3] - 1;
                        }
                        if($v2[2] == "R"){
                            $v1[3] = $v1[3] + 1;
                        }
                        if($v2[2] == "T"){
                            $v1[2] = $v1[2] - 1;
                        }
                        if($v2[2] == "B"){
                            $v1[2] = $v1[2] + 1;
                        }  
                    }
                }
                $index = $v1[2] * $size_map + $v1[3];
                if(in_array($index, $hit_targets)){
                    unset($hit_targets[$index]);
                }

            }

            $my_attack_hit[$k1]["x"] = $v1[2];
            $my_attack_hit[$k1]["y"] = $v1[3];

            for ($i = -1; $i <= 1; $i++) {
                for ($j = -1; $j <= 1; $j++) {
                    $hit_target["x"] = $v1[2] + $i;
                    $hit_target["y"] = $v1[3] + $j;
                    if($hit_target["x"] > -1 && $hit_target["x"] < $size_map && $hit_target["y"] > -1 && $hit_target["y"] < $size_map){
                        $target = $hit_target["x"] * $size_map + $hit_target["y"];
                        array_push($hit_targets, $target);
                    }
                }
            }
        }

        $valid_targets = $available_targets;

        foreach ($valid_targets as $k => $v) {
            if(!in_array($v, $hit_targets)){
                unset($available_targets[$v]);
            }
        }

        if(count($available_targets) != 0){
            $random_target = array_rand($available_targets,1);
            $x = floor($random_target / $size_map);
            $y = $random_target % $size_map;
        } else {
            $random_attack_hit = array_rand($my_attack_hit,1);
            $x = $random_attack_hit["x"] + rand(-1, 1);
            $y = $random_attack_hit["y"] + rand(-1, 1);
        }
        return "A {$x} {$y}";

    } else {
        $random_target = array_rand($available_targets,1);
        $x = floor($random_target / $size_map);
        $y = $random_target % $size_map;
        return "A {$x} {$y}";
    }
        
}