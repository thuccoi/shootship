<?php


function solve1($file = "data.txt", $player = 1) {
    //sizemap data
    $sizemap = 10;
    if (!function_exists('_Thor_readData')) {

        function _Thor_readData($file) {
            //read data.txt
            $data = [];
            $handle = fopen($file, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $line = str_replace('0', '*', $line);
                    $cv = array_map('trim', array_filter(explode(' ', $line)));

                    $arr = [];
                    foreach ($cv as $v) {
                        if ($v) {
                            if ($v == '*') {
                                $v = 0;
                            }
                            $arr[] = $v;
                        }
                    }
                    $data[] = $arr;
                }
            }
            fclose($handle);
            return $data;
        }

    }

    if (!function_exists('_Thor_positionRandom')) {

        function _Thor_positionRandom($sizemap) {
            $r = rand(0, 123454556);
            return $r % $sizemap;
        }

    }

    if (!function_exists('_Thor_getMapEmpty')) {

        //get map empty
        function _Thor_getMapEmpty($sizemap) {
            $map = [];
            for ($i = 0; $i < $sizemap; $i++) {
                for ($j = 0; $j < $sizemap; $j++) {
                    $map[$j + $i * $sizemap] = (object) [
                                "x" => $i,
                                "y" => $j,
                                "status" => 0
                    ];
                }
            }

            return $map;
        }

    }

    if (!function_exists('_Thor_setMapEmpty')) {

        //return map empty
        function _Thor_setMapEmpty($map) {
            $newmap = [];
            foreach ($map as $val) {
                $val->status = 0;
                $newmap[] = $val;
            }
            return $newmap;
        }

    }

    if (!function_exists('_Thor_toPosition')) {

        function _Thor_toPosition($x, $y, $sizemap) {
            return $y + $x * $sizemap;
        }

    }

    if (!function_exists('_Thor_getMapAvailable')) {

        //return index available
        function _Thor_getMapAvailable($map) {
            $availabel = [];
            foreach ($map as $key => $val) {
                if ($val->status == 0) {
                    $availabel[] = $key;
                }
            }
            return $availabel;
        }

    }

    if (!function_exists('_Thor_getPositionShoot')) {

        //return position shoot
        function _Thor_getPositionShoot($map) {
            $availabel = _Thor_getMapAvailable($map);
            $sm = count($availabel);
            if ($sm == 0) {
                return -1;
            }

            return $availabel[_Thor_positionRandom($sm)];
        }

    }
    if (!function_exists('_Thor_shootInMap')) {

        function _Thor_shootInMap($index, $map) {
            $newmap = [];

            foreach ($map as $key => $val) {
                if ($key == $index) {
                    $val->status = 1;
                }

                $newmap[] = $val;
            }

            return $newmap;
        }

    }
    if (!function_exists('_Thor_getDataBeforDirect')) {

        function _Thor_getDataBeforDirect($data, $player) {

            $rs = [];
            for ($i = count($data); $i >= 0; $i--) {
                if (isset($data[$i])) {
                    $dt = $data[$i];

                    if (isset($dt[0]) && isset($dt[1])) {
                        if ($dt[1] == 'D' && $dt[0] != $player) {
                            break;
                        }
                        if ($dt[0] == $player && $dt[1] == 'A') {
                            if (isset($dt[2]) && isset($dt[3])) {
                                $rs[] = (object) [
                                            "x" => $dt[2],
                                            "y" => $dt[3]
                                ];
                            }
                        }
                    }
                }
            }

            return $rs;
        }

    }
    if (!function_exists('_Thor_setShooted')) {

        //shooted
        function _Thor_setShooted($data, $player, $map, $sizemap) {
            $rs = _Thor_getDataBeforDirect($data, $player);
            foreach ($rs as $point) {
                $sm = _Thor_shootInMap($point->y + $point->x * $sizemap, $map);
                if ($sm != -1) {
                    $map = $sm;
                }
            }
            return $map;
        }

    }
    if (!function_exists('_Thor_checkHasHit')) {

        //check has hit
        function _Thor_checkHasHit($data, $player) {

            for ($i = count($data); $i >= 0; $i--) {
                if (isset($data[$i])) {
                    $dt = $data[$i];
                    if (isset($dt[0]) && isset($dt[1])) {
                        if (isset($dt[4])) {
                            if ($dt[4] == 'T' && $dt[1] == 'A' && $dt[0] == $player) {
                                return (object) [
                                            'x' => $dt[2],
                                            'y' => $dt[3]
                                ];
                            }
                        }
                    }
                }
            }

            return FALSE;
        }

    }

    if (!function_exists('_Thor_getHasHit')) {

        function _Thor_getHasHit($data, $player) {
            $hit = _Thor_checkHasHit($data, $player);
            if (!$hit) {
                return -1;
            }

            $rs = [];
            for ($i = count($data); $i >= 0; $i--) {
                if (isset($data[$i])) {
                    $dt = $data[$i];

                    if (isset($dt[0]) && isset($dt[1])) {
                        if (isset($dt[4])) {
                            if ($dt[4] == 'T' && $dt[1] == 'A' && $dt[0] == $player) {
                                break;
                            }
                        }

                        if ($dt[0] != $player && $dt[1] == 'D') {
                            if (isset($dt[2])) {
                                $rs[] = $dt[2];
                            }
                        }
                    }
                }
            }

            $rs = array_reverse($rs);
            foreach ($rs as $dir) {
                switch ($dir) {
                    case 'T':
                        $hit->x = $hit->x - 1;
                        break;
                    case 'R':
                        $hit->y = $hit->y + 1;
                        break;
                    case 'B':
                        $hit->x = $hit->x + 1;
                        break;
                    case 'L':
                        $hit->y = $hit->y - 1;
                        break;
                }
            }


            return $hit;
        }

    }

    if (!function_exists('_Thor_rangeShoot')) {

        function _Thor_rangeShoot($data, $player, $sizemap) {
            $hit = _Thor_getHasHit($data, $player);

            $ps = [];
            for ($i = $hit->x - 2; $i <= $hit->x + 2; $i++) {
                for ($j = $hit->y - 2; $j <= $hit->y + 2; $j++) {
                    if ($i != $hit->x || $j != $hit->y) {
                        $ps[] = (object) [
                                    "x" => $i,
                                    "y" => $j
                        ];
                    }
                }
            }

            $rs = [];

            $hashit = _Thor_getDataBeforDirect($data, $player);


            foreach ($ps as $val) {
                if ($val->x >= 0 && $val->x < $sizemap) {
                    if ($val->y >= 0 && $val->y < $sizemap) {
                        $flat = TRUE;

                        foreach ($hashit as $ht) {
                            if ($ht->x == $val->x && $ht->y == $val->y) {
                                $flat = FALSE;
                                break;
                            }
                        }

                        if ($flat == TRUE) {
                            $rs[] = (object) [
                                        "x" => $val->x,
                                        "y" => $val->y
                            ];
                        }
                    }
                }
            }


            return $rs;
        }

    }

    if (!function_exists('_Thor_isBroken')) {

        function _Thor_isBroken($data, $player) {
            $length = count($data);
            if ($length > 0) {
                $step = 0;
                for ($i = $length; $i >= 0; $i--) {
                    if (isset($data[$i])) {
                        $dt = $data[$i];
                        if ($dt[0] != $player) {
                            if (isset($dt[0]) && $dt[0] != $player && isset($dt[4]) && $dt[4] == 'T') {
                                return (object) [
                                            "x" => $dt[2],
                                            "y" => $dt[3]
                                ];
                            }
                        } else {
                            $step ++;
                            if ($step > 1) {
                                return FALSE;
                            }
                        }
                    }
                }
            }

            return FALSE;
        }

    }

    if (!function_exists('_Thor_shoot')) {

        function _Thor_shoot($file, $player, $sizemap) {
            $data = _Thor_readData($file);
            if (count($data) > 0) {

                $broken = _Thor_isBroken($data, $player);
                if ($broken != FALSE) {
                    $dirs = ['T', 'R', 'B', 'L'];
                    $dir = $dirs[_Thor_positionRandom(4)];
                    switch ($dir) {
                        case 'T':
                            if ($broken->x == 0) {
                                $dir = 'B';
                            }
                            break;
                        case 'R':
                            if ($broken->y == $sizemap - 1) {
                                $dir = 'L';
                            }
                            break;
                        case 'B':
                            if ($broken->x == $sizemap - 1) {
                                $dir = 'T';
                            }
                            break;
                        case 'L':
                            if ($broken->y == 0) {
                                $dir = 'R';
                            }
                            break;
                    }
                    return (object) [
                                "status" => 'D',
                                "dir" => $dir
                    ];
                }
            }

            if (_Thor_checkHasHit($data, $player)) {
                $rs = _Thor_rangeShoot($data, $player, $sizemap);

                if ($rs) {
                    $rd = _Thor_positionRandom(count($rs));
                    if (isset($rs[$rd])) {
                        return (object) [
                                    "status" => "A",
                                    "x" => $rs[$rd]->x,
                                    "y" => $rs[$rd]->y
                        ];
                    }
                }
                $x = _Thor_positionRandom($sizemap);
                $y = _Thor_positionRandom($sizemap);

                return (object) [
                            "status" => "A",
                            "x" => $x,
                            "y" => $y
                ];
            } else {
                $map = _Thor_getMapEmpty($sizemap);
                $map = _Thor_setShooted($data, $player, $map, $sizemap);
                $am = [];
                foreach ($map as $val) {
                    if ($val->status != 1) {
                        $am[] = $val;
                    }
                }

                $rd = _Thor_positionRandom(count($am));

                return (object) [
                            "status" => "A",
                            'x' => $am[$rd]->x,
                            'y' => $am[$rd]->y
                ];
            }
        }

    }


    $shoot = _Thor_shoot($file, $player, $sizemap);
    if ($shoot->status == 'A') {
        $x = _Thor_positionRandom($sizemap);
        $y = _Thor_positionRandom($sizemap);
        if (isset($shoot->x) && isset($shoot->y)) {
            $x = $shoot->x;
            $y = $shoot->y;
        }


        return "A {$x} {$y}";
    } else {
        return "D {$shoot->dir}";
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
