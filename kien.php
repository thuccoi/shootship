<?php

function solveKien($file = "data.txt", $player, $size_map) {

    // Get data
    $data = getData($file);

    // Get history
    $history = getHistory($data, $player);
    $my_turns = $history["my_turns"];
    $my_attack_fails = $history["my_attack_fails"];
    $my_attack_hits = $history["my_attack_hits"];
    $my_moves = $history["my_moves"];
    $enemy_turns = $history["enemy_turns"];
    $enemy_moves = $history["enemy_moves"];
    $enemy_attack_hits = $history["enemy_attack_hits"];

    // Chay neu bi ban trung
    if(count($enemy_attack_hits)){
        if (count($my_moves) < count($enemy_attack_hits) * 3 + 1){
            $diff = $enemy_turns - $my_turns;
            if ($diff < 2){
                return getRun();
            } 
        }
    }

    // Build cac origin map targets khac
    $original_maps = getOriginMaps($size_map);
    $phase_1_targets = getPhase1Targets($size_map);
    $phase_2_targets = getPhase2Targets($size_map);

    // Lua chon toa do ban o map origin
    $available_targets = [];

    // Tinh do dich chuyen cua map
    $translational_map = getTranslationalMap($enemy_moves);

    // Danh sach o da ban o map origin
    $my_mark_origins = getMarkOriginMaps($my_attack_fails, $enemy_moves, $size_map);

    // Khoa muc tieu khi ban trung
    if(count($my_attack_hits)){
        $origin_hits = getOriginHits($my_attack_hits, $enemy_moves, $size_map);
        $origin_hit_targets = getOriginHitTargets($origin_hits, $size_map);

        foreach ($my_mark_origins as $key => $index) {
            if(array_key_exists($index, $origin_hit_targets)){
                unset($origin_hit_targets[$index]);
            }
        }

        // Dich chuyen map origin theo buoc chay cua dich ($translational_map)
        $available_targets = setTranslationalAvailableTargets($origin_hit_targets, $translational_map);

        // Valid toa do nam trong map tuyet doi
        $available_targets = setValidPosition($available_targets, $size_map);

        // Lua chon toa do ban trong available_targets
        if (count($available_targets) == 0){
            return getFixTargets($my_attack_hits, $size_map);
        }

        return getRandomAvailableTargets($available_targets);
    } else {

        // Danh dau cac o da ban o cac origin map
        foreach ($my_mark_origins as $key => $index) {
            if(array_key_exists($index, $phase_1_targets)){
                unset($phase_1_targets[$index]);
            }
            if(array_key_exists($index, $phase_2_targets)){
                unset($phase_2_targets[$index]);
            }
            if(array_key_exists($index, $original_maps)){
                unset($original_maps[$index]);
            }
        }

        if (count($phase_1_targets) > 0){
            $available_targets = $phase_1_targets;
        } else if (count($phase_1_targets) == 0 && count($phase_2_targets) > 0){
            $available_targets = $phase_2_targets;
        } else {
            $available_targets = $original_maps;
        }

        // Dich chuyen map origin theo buoc chay cua dich ($translational_map)
        $available_targets = setTranslationalAvailableTargets($available_targets, $translational_map);

        // Valid toa do nam trong map tuyet doi
        $available_targets = setValidPosition($available_targets, $size_map);

        return getRandomAvailableTargets($available_targets);
    }
}

function getOriginMaps($size_map){
    $original_maps = [];

    // Build $original_maps
    for ($i = 0; $i <= ($size_map - 1); $i++) {
        for ($j = 0; $j <= ($size_map - 1); $j++) {
            $index = $i * $size_map + $j;
            $original_maps[$index] = [
                "x" => $i,
                "y" => $j
            ];
        }
    }
    return $original_maps;
}

function getPhase1Targets($size_map){
    $phase_1_targets = [];

    // Build $phase_1_targets
    for ($i = 0; $i <= ($size_map - 1); $i++) {
        for ($j = 1; $j <= ($size_map - 1); $j = $j + 2) {
            $index = $i * $size_map + $j;
            $phase_1_targets[$index] = [
                "x" => $i,
                "y" => $j
            ];
        }
    }
    return $phase_1_targets;
}

function getPhase2Targets($size_map){
    $phase_2_targets = [];

    // Build $phase_2_targets
    for ($i = 2; $i <= ($size_map - 1); $i = $i + 3) {
        for ($j = 0; $j <= ($size_map - 1); $j = $j + 2) {
            $index = $i * $size_map + $j;
            $phase_2_targets[$index] = [
                "x" => $i,
                "y" => $j
            ];
        }
    }
    return $phase_2_targets;
}

function getHistory($data, $player){
    $me = $player;
    $enemy = $player == 1 ? 2 : 1;

    $history = [
        "my_turns" => 0,
        "my_attack_fails" => [],
        "my_attack_hits" => [],
        "my_moves" => [],
        "enemy_turns" => 0,
        "enemy_moves" => [],
        "enemy_attack_hits" => []
    ];

    foreach ($data as $k => $v) {
        if($v[0] == $me){
            $history["my_turns"] += 1;
        }

        if($v[0] == $me && $v[1] == "A"){
            $history["my_attack_fails"][$k] = $v;
        }

        if($v[0] == $me && $v[1] == "D" && $v[2] != "0"){
            $history["my_moves"][$k] = $v;
        }

        if($v[0] == $me && $v[1] == "A" && $v[4] == "T"){
            $history["my_attack_hits"][$k] = $v;
        }

        if($v[0] == $enemy){
            $history["enemy_turns"] += 1;
        }

        if($v[0] == $enemy && $v[1] == "D" && $v[2] != "0"){
            $history["enemy_moves"][$k] = $v;
        }

        if($v[0] == $enemy && $v[1] == "A" && $v[4] == "T"){
            $history["enemy_attack_hits"][$k] = $v;
        }
    }
    return $history;
}

function getTranslationalMap($enemy_moves){

    // Tinh $translational_map
    $translational_map = [
        "L" => 0,
        "R" => 0,
        "T" => 0,
        "B" => 0
    ];

    if(count($enemy_moves)){
        foreach ($enemy_moves as $k2 => $v2) {
            if($v2[2] == "L"){
                $translational_map["L"] = $translational_map["L"] + 1;
            }
            if($v2[2] == "R"){
                $translational_map["R"] = $translational_map["R"] + 1;
            }
            if($v2[2] == "T"){
                $translational_map["T"] = $translational_map["T"] + 1;
            }
            if($v2[2] == "B"){
                $translational_map["B"] = $translational_map["B"] + 1;
            } 
        }
    }
    return $translational_map;
}

function getMarkOriginMaps($my_attack_fails, $enemy_moves, $size_map){

    $my_mark_origins = [];

    // Danh sach o da ban o map origin
    foreach ($my_attack_fails as $k1 => $v1) {

        if(count($enemy_moves)){
            foreach ($enemy_moves as $k2 => $v2) {
                if($k1 > $k2){
                    if($v2[2] == "L"){
                        $v1[3] = $v1[3] + 1;
                    }
                    if($v2[2] == "R"){
                        $v1[3] = $v1[3] - 1;
                    }
                    if($v2[2] == "T"){
                        $v1[2] = $v1[2] + 1;
                    }
                    if($v2[2] == "B"){
                        $v1[2] = $v1[2] - 1;
                    } 
                }
            }
        }

        if($v1[2] > -1 && $v1[2] < $size_map && $v1[3] > -1 && $v1[3] < $size_map){
            $index = $v1[2] * $size_map + $v1[3];
            array_push($my_mark_origins, $index);
        }
        
    }
    return $my_mark_origins;
}

function setTranslationalAvailableTargets($available_targets, $translational_map){
    $temp = [];
    foreach ($available_targets as $key => $val) {
        $temp[$key] = [
            "x" => $val["x"] + $translational_map["B"] - $translational_map["T"], 
            "y" => $val["y"] + $translational_map["R"] - $translational_map["L"]
        ];
    }
    return $temp;
}

function setValidPosition($targets, $size_map){
    $temp = [];
    foreach ($targets as $key => $val) {
        if($val["x"] > -1 && $val["x"] < $size_map && $val["y"] > -1 && $val["y"] < $size_map){
            $temp[$key] = $val;
        }
    }
    return $temp;
}

function getOriginHits($my_attack_hits, $enemy_moves, $size_map){
    $origin_hits = [];

    // Danh sach o da ban trung o map origin
    if (count($my_attack_hits)){
        foreach ($my_attack_hits as $k1 => $v1) {

            if(count($enemy_moves)){
                foreach ($enemy_moves as $k2 => $v2) {
                    if($k1 > $k2){
                        if($v2[2] == "L"){
                            $v1[3] = $v1[3] + 1;
                        }
                        if($v2[2] == "R"){
                            $v1[3] = $v1[3] - 1;
                        }
                        if($v2[2] == "T"){
                            $v1[2] = $v1[2] + 1;
                        }
                        if($v2[2] == "B"){
                            $v1[2] = $v1[2] - 1;
                        } 
                    }
                }
            }

            $index = $v1[2] * $size_map + $v1[3];
            $origin_hits[$index] = [
                "x" => $v1[2],
                "y" => $v1[3]
            ];
            
        }
    }
    return $origin_hits;
}

function getOriginHitTargets($origin_hits, $size_map){
    $origin_hit_targets = [];

    if(count($origin_hits)){
        
        foreach ($origin_hits as $k1 => $v1) {

            // 1
            $x = $v1["x"] - 1;
            $y = $v1["y"] - 1;
            if ($x > -1 && $y > -1){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 2
            $x = $v1["x"] - 1;
            $y = $v1["y"];
            if ($x > -1){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 3
            $x = $v1["x"] - 1;
            $y = $v1["y"] + 1;
            if ($x > -1 && $y < $size_map){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 4
            $x = $v1["x"];
            $y = $v1["y"] + 1;
            if ($y < $size_map){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 5
            $x = $v1["x"] + 1;
            $y = $v1["y"] + 1;
            if ($x < $size_map && $y < $size_map){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 6
            $x = $v1["x"] + 1;
            $y = $v1["y"];
            if ($x < $size_map){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 7
            $x = $v1["x"] + 1;
            $y = $v1["y"] - 1;
            if ($x < $size_map && $y > -1){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

            // 8
            $x = $v1["x"];
            $y = $v1["y"] - 1;
            if ($y > -1){
                $index = $x * $size_map + $y;
                $origin_hit_targets[$index] = [
                    "x" => $x,
                    "y" => $y
                ];
            }

        }

    } 

    return $origin_hit_targets;
}

function getRandomAvailableTargets($available_targets){
    $random_index = array_rand($available_targets,1);
    $x = $available_targets[$random_index]["x"];
    $y = $available_targets[$random_index]["y"];
    return "A {$x} {$y}";
}

function getRun(){
    // $rand_strategy = rand(0, 2);
    // if ($rand_strategy){
        $dir = 'T';
        $r = rand(0, 3);
        if ($r == 0) {
            $dir = 'T';
        } elseif ($r == 1) {
            $dir = 'L';
        } elseif ($r == 2) {
            $dir = 'B';
        } elseif ($r == 3) {
            $dir = 'R';
        }
    // } else {
    //     $direction = (rand(0, 1)) ? array('L', 'R') : array('B', 'T');
    //     $dir = array_rand($direction);
    // }
    
    return "D {$dir}";
}

function getFixTargets($my_attack_hits, $size_map){
    $random_attack_hit = reset($my_attack_hits);
    do {
        $x = $random_attack_hit[2] + rand(-3, 3);
        $y = $random_attack_hit[3] + rand(-3, 3);
    } while (!($x > -1 && $x < $size_map && $y > -1 && $y < $size_map));
    
    return "A {$x} {$y}";
}