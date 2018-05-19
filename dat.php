<?php

    /**
     * @desc: Shot ship
     * object: {map:n^2, enermy:{position, direction}, wrap:{map, scope}, shot:{0,1,2,3}}
     */
//include 'war.php';
//$n = 30;
//$file = "data.txt";
//$player = 2;
//$dir = (object) ['top' => 0, 'right' => 9, 'bottom' => 0, 'left' => 0];
//$pos = (object) ['x' => 0, 'y' => 0];
//$map = dat_initMap();
//echo '<pre>';

    function dat_map_k($n, $jump) {
        $map = dat_initMap($n, 0);
        for ($i = $jump - 1; $i < $n - $jump + 2; $i = $i + $jump) {
            for ($j = $jump - 1; $j < $n - $jump + 2; $j = $j + $jump) {
                $map[$i][$j] = 1;
            }
        }
        return $map;
    }

//=============Init
    function dat_option($pos) {
        return [
            (object) ['x' => $pos->x - 1, 'y' => $pos->y + 1],
            (object) ['x' => $pos->x + 1, 'y' => $pos->y + 1],
            (object) ['x' => $pos->x + 1, 'y' => $pos->y - 1],
            (object) ['x' => $pos->x - 1, 'y' => $pos->y - 1],
            (object) ['x' => $pos->x - 1, 'y' => $pos->y],
            (object) ['x' => $pos->x, 'y' => $pos->y + 1],
            (object) ['x' => $pos->x + 1, 'y' => $pos->y],
            (object) ['x' => $pos->x, 'y' => $pos->y - 1],
            (object) ['x' => $pos->x - 2, 'y' => $pos->y],
            (object) ['x' => $pos->x, 'y' => $pos->y + 2],
            (object) ['x' => $pos->x + 2, 'y' => $pos->y],
            (object) ['x' => $pos->x, 'y' => $pos->y - 2],
            (object) ['x' => $pos->x - 2, 'y' => $pos->y + 1],
            (object) ['x' => $pos->x - 1, 'y' => $pos->y + 2],
            (object) ['x' => $pos->x + 1, 'y' => $pos->y + 2],
            (object) ['x' => $pos->x + 2, 'y' => $pos->y + 1],
            (object) ['x' => $pos->x + 2, 'y' => $pos->y - 1],
            (object) ['x' => $pos->x + 1, 'y' => $pos->y - 2],
            (object) ['x' => $pos->x - 1, 'y' => $pos->y - 2],
            (object) ['x' => $pos->x - 2, 'y' => $pos->y - 1],
            (object) ['x' => $pos->x - 2, 'y' => $pos->y + 2],
            (object) ['x' => $pos->x + 2, 'y' => $pos->y + 2],
            (object) ['x' => $pos->x + 2, 'y' => $pos->y - 2],
            (object) ['x' => $pos->x - 2, 'y' => $pos->y - 2]
        ];
    }

    function dat_checkError($file = "data.txt", $player = 2) {
        $steps = getData($file);
        $length = count($steps);
        $before = null;
        $present = null;
        for ($i = 2; $i < $length; $i++) {
            if ($steps[$i][0] == $player && $steps[$i - 2][0] == $player) {
                if ($steps[$i][1] == 'A' && $steps[$i - 2][1] == 'A') {
                    if ($steps[$i][2] == $steps[$i - 2][2] && $steps[$i][3] == $steps[$i - 2][3]) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

//=============Map
    function dat_initMap($n = 10, $val = 1) {
        $map = array(array());
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $map[$i][$j] = $val;
            }
        }
        return $map;
    }

    function dat_showMap($map, $n = 10) {
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                echo $map[$i][$j] . ' ';
            }
            echo '<br>';
        }
    }

//=============Enermy
    function dat_positionEnermy($file = "data.txt", $player = 2) {
        $enermy = $player % 2 + 1;
        $steps = getData($file);
        $length = count($steps);
        $count = 0;
        $pos = null;
        for ($i = 0; $i < $length; $i++) {
            //player fire true
            if ($steps[$i][0] == $player && $steps[$i][1] == 'A' && isset($steps[$i][4]) && $steps[$i][4] == 'T') {
                $count ++;
                $pos = (object) ['x' => $steps[$i][2], 'y' => $steps[$i][3]];
                break;
            }
        }
        return $pos;
    }

    function dat_positionEnermySub($file = "data.txt", $player = 2) {
        $enermy = $player % 2 + 1;
        $steps = getData($file);
        $length = count($steps);
        $count = 0;
        $pos = null;
        for ($i = 0; $i < $length; $i++) {
            //player fire true
            if ($steps[$i][0] == $player && $steps[$i][1] == 'A' && isset($steps[$i][4]) && $steps[$i][4] == 'T') {
                $count ++;
                $pos = (object) ['x' => $steps[$i][2], 'y' => $steps[$i][3]];
            }
        }
        return $pos;
    }

    function dat_directionEnermyMap($file = "data.txt", $player = 2) {
        $enermy = $player % 2 + 1;
        $steps = getData($file);
        $length = count($steps);
        $direction = (object) [
                    'top' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'right' => 0
        ];
        for ($i = 0; $i < $length; $i++) {
            if ($steps[$i][0] == $enermy) {
                if ($steps[$i][1] == 'D') {
                    switch ($steps[$i][2]) {
                        case 'T':
                            $direction->top++;
                            break;
                        case 'B':
                            $direction->bottom++;
                            break;
                        case 'L':
                            $direction->left++;
                            break;
                        case 'R':
                            $direction->right++;
                            break;
                        default :break;
                    }
                }
            }
        }

        if ($direction->top > $direction->bottom) {
            $direction->top = $direction->top - $direction->bottom;
            $direction->bottom = 0;
        } else if ($direction->top < $direction->bottom) {
            $direction->bottom = $direction->bottom - $direction->top;
            $direction->top = 0;
        } else {
            $direction->top = $direction->bottom = 0;
        }
        if ($direction->left > $direction->right) {
            $direction->left = $direction->left - $direction->right;
            $direction->right = 0;
        } else if ($direction->left < $direction->right) {
            $direction->right = $direction->right - $direction->left;
            $direction->left = 0;
        } else {
            $direction->left = $direction->right = 0;
        }
        return $direction;
    }

    function dat_directionEnermyScope($file = "data.txt", $player = 2) {
        $enermy = $player % 2 + 1;
        $steps = getData($file);
        $length = count($steps);
        $count = 0;
        $dir = (object) ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
        for ($i = 0; $i < $length; $i++) {
            //player fire true
            if ($steps[$i][0] == $player && $steps[$i][1] == 'A' && isset($steps[$i][4]) && $steps[$i][4] == 'T') {
                $count ++;
            }
            //enermy move
            if ($count >= 1) {
                if ($steps[$i][0] == $enermy && $steps[$i][1] == 'D' && isset($steps[$i][2])) {
                    $d = $steps[$i][2];
                    if ($d == 'T') {
                        $dir->top++;
                    } else if ($d == 'R') {
                        $dir->right++;
                    } else if ($d == 'B') {
                        $dir->bottom++;
                    } else if ($d == 'L') {
                        $dir->left++;
                    }
                }
            }
        }
        //convert enermy move.
        if ($dir->top > $dir->bottom) {
            $dir->top = $dir->top - $dir->bottom;
            $dir->bottom = 0;
        } else if ($dir->top < $dir->bottom) {
            $dir->bottom = $dir->bottom - $dir->top;
            $dir->top = 0;
        } else {
            $dir->top = $dir->bottom = 0;
        }
        if ($dir->right > $dir->left) {
            $dir->right = $dir->right - $dir->left;
            $dir->left = 0;
        } else if ($dir->right < $dir->left) {
            $dir->left = $dir->left - $dir->right;
            $dir->right = 0;
        } else {
            $dir->right = $dir->left = 0;
        }
        return $dir;
    }

//=============Wrap
    function dat_limitMap($map, $dir, $n = 10) {
        if ($dir->top > 0) {
            for ($i = $n - $dir->top; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $map[$i][$j] = 0;
                }
            }
        } else if ($dir->bottom > 0) {
            for ($i = 0; $i < $dir->bottom; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $map[$i][$j] = 0;
                }
            }
        }

        if ($dir->right > 0) {
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $dir->right; $j++) {
                    $map[$i][$j] = 0;
                }
            }
        } else if ($dir->left > 0) {
            for ($i = $n - $dir->left; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $map[$i][$j] = 0;
                }
            }
        }

        return $map;
    }

    function dat_limitScope($pos, $dir, $n = 10) {
        $area = dat_initMap($n);
    }

    function dat_gridMapAbsolute($file = "data.txt", $player = 2, $n = 10) {
        $enermy = $player % 2 + 1;
        $map = dat_initMap($n);
        $step = getData($file);
        $length = count($step);
        $top = $right = $bottom = $left = 0;
        for ($i = 0; $i < $length; $i++) {
            if ($step[$i][0] == $enermy && $step[$i][1] = 'D') {
                if (isset($step[$i][2])) {
                    if ($step[$i][2] == 'T') {
                        $top++;
                    } else if ($step[$i][2] == 'R') {
                        $right++;
                    } else if ($step[$i][2] == 'B') {
                        $bottom++;
                    } else if ($step[$i][2] == 'L') {
                        $left++;
                    }
                }
            }
            if ($step[$i][0] == $player && $step[$i][1] == 'A') {
                $x = $step[$i][2];
                $y = $step[$i][3];
                if (isset($x) && isset($y)) {
                    $map[$x + $top - $bottom][$y - $right + $left] = 0;
                }
            }
        }
        return $map;
    }

//$pos = dat_positionEnermy($file, $player);
//print_r($pos);
//print_r($dir);
////$map = dat_limitMap($map, $dir, $n);
//$map = dat_gridMapAbsolute($file, $player, $n);
//dat_showMap($map);
//$map = dat_limitMap($map, $dir);
//dat_showMap($map);
//exit;
//=============Random shoot.
    function dat_funnyAttack($n = 10) {
        $x = rand(0, $n - 1);
        $y = rand(0, $n - 1);
        return "A {$x} {$y}";
    }

    function dat_randomAttack($file = "data.txt", $player = 2, $n = 10) {
        $grid_map_abs = dat_gridMapAbsolute($file, $player, $n);
        $map1 = dat_map_k($n, 1);
        $map2 = dat_map_k($n, 2);
        $map3 = dat_map_k($n, 3);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($map3[$i][$j] == 1 && $grid_map_abs[$i][$j] == 1) {
                    return "A {$i} {$j}";
                }
            }
        }

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($map2[$i][$j] == 1 && $grid_map_abs[$i][$j] == 1) {
                    return "A {$i} {$j}";
                }
            }
        }

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($map1[$i][$j] == 1 && $grid_map_abs[$i][$j] == 1) {
                    return "A {$i} {$j}";
                }
            }
        }

        return dat_funnyAttack($n);
    }

//$some = dat_randomAttack($file, $player, $n);
//print_r($some);
//=============Shot
    function dat_stupidAttack($file = "data.txt", $player = 2, $n = 10) {
        $pos = dat_positionEnermy($file, $player);
        if (!isset($pos)) {
            return dat_randomAttack($file, $player, $n);
        }
        if (dat_checkError($file, $player)) {
            return dat_funnyAttack($n);
        }
        $grid_map_abs = dat_gridMapAbsolute($file, $player, $n);
        $option = dat_option($pos);
        $length = count($option);
        for ($i = 0; $i < $length; $i++) {
            if ($option[$i]->x >= 0 && $option[$i]->x < $n && $option[$i]->y >= 0 && $option[$i]->y < $n) {
                if ($grid_map_abs[$option[$i]->x][$option[$i]->y] == 1) {
                    return "A {$option[$i]->x} {$option[$i]->y}";
                }
            }
        }

        $x_i = ($pos->x - 2) >= 0 ? ($pos->x - 2) : 0;
        $x_s = ($pos->x + 2) < $n ? ($pos->x + 2) : $n;
        $y_i = ($pos->y - 2) >= 0 ? ($pos->y - 2) : 0;
        $y_s = ($pos->y + 2) < $n ? ($pos->y + 2) : $n;
        for ($i = $x_i; $i < $x_s; $i++) {
            for ($j = $y_i; $j < $y_s; $j++) {
                if ($grid_map_abs[$i][$j] == 1) {
                    return "A {$i} {$j}";
                }
            }
        }
        return dat_funnyAttack($n);
    }

//Sumary=================================
    function solveDat($file = "data.txt", $player = 2, $n = 10, $uplay = 'thor') {
        return dat_stupidAttack($file, $player, $n);
    }
    