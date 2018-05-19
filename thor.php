<?php

    function solveThor($file = "data.txt", $player = 1, $sizemap) {
        if (!function_exists('_Thor_readData')) {

            function _Thor_readData($file) {
                //read data.txt
                $data = [];
                $handle = fopen($file, "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $line = str_replace(' 0', '*', $line);
                        if ($line[0] == '0') {
                            $line[0] = '*';
                        }

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
                    fclose($handle);
                }

                return $data;
            }

        }

        if (!function_exists('_Thor_positionRandom')) {

            function _Thor_positionRandom($sizemap) {
                $r = rand(0, 123456789);
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

        if (!function_exists('_Thor_moveArray')) {

            function _Thor_moveArray($arr, $dir) {
                $newarr = [];
                foreach ($arr as $val) {
                    switch ($dir) {
                        case 'T':
                            $val->x--;
                            break;
                        case 'R':
                            $val->y++;
                            break;
                        case 'B':
                            $val->x++;
                            break;
                        case 'L':
                            $val->y--;
                            break;
                    }
                    $newarr[] = (object) [
                                "x" => $val->x,
                                "y" => $val->y
                    ];
                }

                return $newarr;
            }

        }

        if (!function_exists('_Thor_getDataBeforDirect')) {

            function _Thor_getDataBeforDirect($data, $player, $sizemap) {

                $rs = [];
                foreach ($data as $dt) {
                    if (isset($dt[0]) && isset($dt[1])) {
                        if ($dt[1] == 'D' && $dt[0] != $player) {
                            //move follow ship deferent
                            $rs = _Thor_moveArray($rs, $dt[2]);
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

                $newrs = [];
                foreach ($rs as $val) {
                    if ($val->x >= 0 && $val->x < $sizemap && $val->y >= 0 && $val->y < $sizemap) {
                        $newrs[] = $val;
                    }
                }

                return $newrs;
            }

        }
        if (!function_exists('_Thor_setShooted')) {

            //shooted
            function _Thor_setShooted($data, $player, $map, $sizemap) {
                $rs = _Thor_getDataBeforDirect($data, $player, $sizemap);


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

                $listhit = [];
                for ($i = count($data); $i >= 0; $i--) {
                    if (isset($data[$i])) {
                        $dt = $data[$i];
                        if (isset($dt[0]) && isset($dt[1])) {
                            if (isset($dt[4])) {
                                if ($dt[4] == 'T' && $dt[1] == 'A' && $dt[0] == $player) {

                                    $listhit[] = (object) [
                                                'x' => $dt[2],
                                                'y' => $dt[3]
                                    ];
                                }
                            }
                        }
                    }
                }

                return $listhit;
            }

        }

        if (!function_exists('_Thor_moveHit')) {

            function _Thor_moveHit($data, $hit, $player) {

                $rs = [];
                for ($i = count($data); $i >= 0; $i--) {
                    if (isset($data[$i])) {
                        $dt = $data[$i];

                        if (isset($dt[0]) && isset($dt[1])) {
                            if (isset($dt[4])) {
                                if ($hit->x == $dt[2] && $hit->y == $dt[3] && $dt[4] == 'T' && $dt[1] == 'A' && $dt[0] == $player) {
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

                $newhit = (object) [
                            'x' => $hit->x,
                            'y' => $hit->y
                ];

                foreach ($rs as $dir) {
                    switch ($dir) {
                        case 'T':
                            $newhit->x = $newhit->x - 1;
                            break;
                        case 'R':
                            $newhit->y = $newhit->y + 1;
                            break;
                        case 'B':
                            $newhit->x = $newhit->x + 1;
                            break;
                        case 'L':
                            $newhit->y = $newhit->y - 1;
                            break;
                    }
                }

                return $newhit;
            }

        }
        if (!function_exists('_Thor_getHasHit')) {

            function _Thor_getHasHit($data, $player) {
                $hits = _Thor_checkHasHit($data, $player);
                if (count($hits) == 0) {
                    return -1;
                }


                $newhit = [];
                foreach ($hits as $hit) {
                    $newhit[] = _Thor_moveHit($data, $hit, $player);
                }

                return $newhit;
            }

        }


        if (!function_exists('_Thor_range')) {

            function _Thor_range($hit, $data, $sizemap, $player) {
                $ps = [];
                for ($i = $hit->x - 1; $i <= $hit->x + 1; $i++) {
                    for ($j = $hit->y - 1; $j <= $hit->y + 1; $j++) {
                        if ($i != $hit->x || $j != $hit->y) {
                            $ps[] = (object) [
                                        "x" => $i,
                                        "y" => $j
                            ];
                        }
                    }
                }


                $rs = [];

                foreach ($ps as $val) {
                    if ($val->x >= 0 && $val->x < $sizemap) {
                        if ($val->y >= 0 && $val->y < $sizemap) {

                            $rs[] = (object) [
                                        "x" => $val->x,
                                        "y" => $val->y
                            ];
                        }
                    }
                }


                return $rs;
            }

        }

        if (!function_exists('_Thor_unique')) {

            function _Thor_unique($arr, $sizemap) {
                $un = [];
                foreach ($arr as $val) {
                    $un[$val->y + $val->x * $sizemap] = $val;
                }
                $au = [];
                foreach ($un as $val) {
                    $au[] = $val;
                }
                return $au;
            }

        }

        if (!function_exists('_Thor_rangeShoot')) {

            function _Thor_rangeShoot($data, $player, $sizemap) {
                $hits = _Thor_getHasHit($data, $player);

                $hashit = _Thor_getDataBeforDirect($data, $player, $sizemap);

                $rss = [];
                foreach ($hits as $hit) {
                    $rs = _Thor_range($hit, $data, $sizemap, $player);
                    foreach ($rs as $r) {
                        $rss[] = $r;
                    }
                }

                $rs = [];
                foreach ($rss as $val) {
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


                return _Thor_unique($rs, $sizemap);
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
                                if ($step > rand(0, 1)) {
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
//            if (count($data) > 0) {
//
//                $broken = _Thor_isBroken($data, $player);
//
//                if ($broken != FALSE) {
//                    $dirs = ['T', 'R', 'B', 'L'];
//                    $dir = $dirs[_Thor_positionRandom(4)];
//                    switch ($dir) {
//                        case 'T':
//                            if ($broken->x == 0) {
//                                $dir = 'B';
//                            }
//                            break;
//                        case 'R':
//                            if ($broken->y == $sizemap - 1) {
//                                $dir = 'L';
//                            }
//                            break;
//                        case 'B':
//                            if ($broken->x == $sizemap - 1) {
//                                $dir = 'T';
//                            }
//                            break;
//                        case 'L':
//                            if ($broken->y == 0) {
//                                $dir = 'R';
//                            }
//                            break;
//                    }
//                    return (object) [
//                                "status" => 'D',
//                                "dir" => $dir
//                    ];
//                }
//            }

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
                    $nmap = _Thor_setShooted($data, $player, $map, $sizemap);

                    $haft_sm = (int) ($sizemap / 2);
                    $location = (object) [
                                'loc1' => 0,
                                'loc2' => 0,
                                'loc3' => 0,
                                'loc4' => 0
                    ];

                    foreach ($nmap as $val) {

                        //balance location
                        if ($val->status == 1) {
                            if ($val->x < $haft_sm) {
                                if ($val->y < $haft_sm) {
                                    $location->loc1 ++;
                                } else {
                                    $location->loc2 ++;
                                }
                            } else {
                                if ($val->y < $haft_sm) {
                                    $location->loc3 ++;
                                } else {
                                    $location->loc4 ++;
                                }
                            }
                        }

                        if ($val->x % 2 == 0 && $val->y % 3 < 2) {
                            $val->status = 1;
                        }

                        if ($val->status != 1) {
                            $am[] = $val;
                        }
                    }

                    $minloc = $sizemap * $sizemap;
                    $inlocation = [];
                    if ($minloc > $location->loc1) {

                        $minloc = $location->loc1;
                        foreach ($am as $val) {
                            if ($val->x < $haft_sm && $val->y < $haft_sm) {
                                $inlocation [] = $val;
                            }
                        }
                    }

                    if ($minloc > $location->loc2) {

                        $inlocation = [];
                        $minloc = $location->loc2;
                        foreach ($am as $val) {
                            if ($val->x < $haft_sm && $val->y >= $haft_sm) {
                                $inlocation [] = $val;
                            }
                        }
                    }

                    if ($minloc > $location->loc3) {

                        $inlocation = [];
                        $minloc = $location->loc3;
                        foreach ($am as $val) {
                            if ($val->x >= $haft_sm && $val->y < $haft_sm) {
                                $inlocation [] = $val;
                            }
                        }
                    }

                    if ($minloc > $location->loc4) {

                        $inlocation = [];
                        $minloc = $location->loc4;
                        foreach ($am as $val) {
                            if ($val->x >= $haft_sm && $val->y >= $haft_sm) {
                                $inlocation [] = $val;
                            }
                        }
                    }

                    $rd = _Thor_positionRandom(count($inlocation));
                    if (isset($inlocation[$rd]) && $inlocation[$rd]->x >= 0 && $inlocation[$rd]->x < $sizemap && $inlocation[$rd]->y >= 0 && $inlocation[$rd]->y < $sizemap) {
                        return (object) [
                                    "status" => "A",
                                    'x' => $inlocation[$rd]->x,
                                    'y' => $inlocation[$rd]->y
                        ];
                    } else {
                        $x = _Thor_positionRandom($sizemap);
                        $y = _Thor_positionRandom($sizemap);

                        return (object) [
                                    "status" => "A",
                                    "x" => $x,
                                    "y" => $y
                        ];
                    }
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
    