<?php

//File
function dat_getData($file = "data.txt") {
    $inputfile = [];
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
            $inputfile[] = $arr;
        }
    }

    fclose($handle);
    return $inputfile;
}

//Display
function showArea($area, $n = 10) {
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            echo $area[$i][$j] . ' ';
        }
        echo '<br>';
    }
}

//Direction
function directionGo($file, $player = 1) {
    $steps = dat_getData($file);
    $length = count($steps);
    $direction = (object) [
                'top' => 0,
                'bottom' => 0,
                'left' => 0,
                'right' => 0
    ];
    for ($i = 0; $i < $length; $i++) {
        if ($steps[$i][0] == $player) {
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
    }
    if ($direction->left > $direction->right) {
        $direction->left = $direction->left - $direction->right;
        $direction->right = 0;
    } else if ($direction->left < $direction->right) {
        $direction->right = $direction->right - $direction->left;
        $direction->left = 0;
    }
    return $direction;
}

//Area
function initArea($n = 10, $val = 1) {
    $area = array(array());
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $area[$i][$j] = $val;
        }
    }
    return $area;
}

function limitAreaEnermy($area, $direction_enermy, $n = 10) {
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($direction_enermy->top > 0) {
                if ($i >= $n - $direction_enermy->top) {
                    $area[$i][$j] = 0;
                }
            } else if ($direction_enermy->bottom > 0) {
                if ($i < $direction_enermy->bottom) {
                    $area[$i][$j] = 0;
                }
            }

            if ($direction_enermy->left > 0) {
                if ($j >= $n - $direction_enermy->left) {
                    $area[$i][$j] = 0;
                }
            } else if ($direction_enermy->right > 0) {
                if ($j < $direction_enermy->right) {
                    $area[$i][$j] = 0;
                }
            }
        }
    }
    return $area;
}

//Trigger
function countAttackTrue($file = "data.txt", $player = 2) {
    $steps = dat_getData($file);
    $length = count($steps);
    $count = 0;
    for ($i = 0; $i < $length; $i++) {
        if ($steps[$i][0] == $player && $steps[$i][4] == 'T') {
            $GLOBALS['first_id'] = $i;
//            print_r($i);
            $count ++;
        }
    }
    return $count;
}

function setFirstPossisionEnermyAttackTrue($file = "data.txt", $player = 2) {
    $enermy = $player % 2 + 1;
    if (!isset($GLOBALS['dat_enermy_pos'])) {
        $pos = (object) [
                    'x' => -1,
                    'y' => -1
        ];
        $steps = dat_getData($file);
        $length = count($steps);
        $flag = 0;
        if (countAttackTrue($file, $player) == 1) {
            $length = dat_getData($file);
            for ($i = 0; $i < $length; $i++) {
                if ($steps[$i][0] == $player && $steps[$i][4] == 'T') {
                    $flag = $i;
                    $pos = (object) [
                                'x' => $steps[$i][2],
                                'y' => $steps[$i][3]
                    ];
                    $GLOBALS['dat_enermy_pos'] = $pos;
                    return true;
                }
            }
        }
        return false;
    }

    return true;
}

function setScopeEnermyFirstAttackTrue($n = 10, $file = "data.txt", $player = 2) {
    $pos = $GLOBALS['dat_enermy_pos'];
    if (!isset($GLOBALS['dat_enermy_scope'])) {
        $GLOBALS['dat_enermy_scope'] = initArea($n);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i < $pos->x - 2 || $i > $pos->x + 2 || $j < $pos->y - 2 || $j > $pos->y + 2) {
                    $GLOBALS['dat_enermy_scope'][$i][$j] = 0;
                    $GLOBALS['dat_enermy_scope'][$i][$j] = 0;
                }
            }
        }
        $GLOBALS['dat_enermy_scope_bak'] = $GLOBALS['dat_enermy_scope'];
        return true;
    }
}

function moveScope($direction, $n = 10) {
    $scope = $GLOBALS['dat_enermy_scope'];
    $pos = $GLOBALS['dat_enermy_pos'];
    $temp_scope = initArea($n, 0);
    $new_pos = $pos;
    switch ($direction) {
        case 'T':
            if ($new_pos->x > 0) {
                $new_pos->x = $new_pos->x - 1;
            }
            break;
        case 'B':
            if ($new_pos->x < $n) {
                $new_pos->x = $new_pos->x + 1;
            }
            break;
        case 'L':
            if ($new_pos->y > 0) {
                $new_pos->y = $new_pos->y - 1;
            }
            break;
        case 'R':
            if ($new_pos->y < $n) {
                $new_pos->y = $new_pos->y + 1;
            }
            break;
        default :
            break;
    }

    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($i >= $new_pos->x - 2 && $i <= $new_pos->x + 2 && $j >= $new_pos->y - 2 && $j <= $new_pos->y + 2) {
                if ($direction == 'T') {
                    if (isset($scope[$i + 1][$j])) {
                        $temp_scope[$i][$j] = $scope[$i + 1][$j];
                    } else {
                        $temp_scope[$i][$j] = 0;
                    }
                } else
                if ($direction == 'B') {
                    if (isset($scope[$i - 1][$j])) {
                        $temp_scope[$i][$j] = $scope[$i - 1][$j];
                    } else {
                        $temp_scope[$i][$j] = 0;
                    }
                } else
                if ($direction == 'L') {
                    if (isset($scope[$i][$j + 1])) {
                        $temp_scope[$i][$j] = $scope[$i][$j + 1];
                    } else {
                        $temp_scope[$i][$j] = 0;
                    }
                } else
                if ($direction == 'R') {
                    if (isset($scope[$i][$j - 1])) {
                        $temp_scope[$i][$j] = $scope[$i][$j - 1];
                    } else {
                        $temp_scope[$i][$j] = 0;
                    }
                }
            }
        }
    }

    $GLOBALS['dat_enermy_pos'] = $new_pos;
    $GLOBALS['dat_enermy_scope'] = $temp_scope;
}

function changeScopeEnermyWhenMove($n = 10, $file = "data.txt", $player = 2) {
    $enermy = $player % 2 + 1;
    $steps = dat_getData($file);
    $length = count($steps);
    $flag = 0;
    for ($i = $length - 1; $i < $length; $i++) {
        if ($flag == 0) {
            if ($steps[$i][0] == $player && $steps[$i][4] == 'T') {
                $flag = 1;
            }
        } else {
            if ($steps[$i][0] == $enermy && $steps[$i][1] == 'D') {
                moveScope($steps[$i][2], $n);
            }
        }
    }
}

//Solve---------------------
function fullAttack($file = "data.txt", $player = 2, $n = 10) {
    $enermy_index = $player % 2 + 1;

    $area = initArea();

    $direction_enermy = directionGo($file, $enermy_index);
    $limit_area = limitAreaEnermy($area, $direction_enermy);
    for ($x = 0; $x < $n; $x++) {
        for ($y = 0; $y < $n; $y++) {
            if ($limit_area[$x][$y] == 1) {
                return "A {$x} {$y}";
            }
        }
    }
    $x = rand(0, $n - 1);
    $y = rand(0, $n - 1);
    return "A {$x} {$y}";
}

function sinceAttackTrue($n = 10, $file = "data.txt", $player = 2) {
    $steps = dat_getData($file);
    $length = count($steps);
    if (!isset($GLOBALS['dat_flag'])) {
        $count = countAttackTrue($file = "data.txt", $player = 2);
        if ($count >= 1) {
            $GLOBALS['dat_flag'] = 1;
        } else {
            if (isset($GLOBALS['arr_op'])) {
                for ($i = 0; $i < count($GLOBALS['arr_op']); $i++) {
                    if ($GLOBALS['arr_op'][$i]->status == 1) {
                        return 'A ' . $GLOBALS['arr_op'][$i]->x . ' ' . $GLOBALS['arr_op'][$i]->y;
                    }
                }
            }

            return fullAttack();
        }
    }
    if ($GLOBALS['dat_flag'] >= 1) {
        if (!isset($GLOBALS['dat_begin_attack'])) {
            $GLOBALS['dat_begin_attack'] = 1;
            //$GLOBALS['dat_enermy_pos'] = 
            setFirstPossisionEnermyAttackTrue($file, $player);
            //$GLOBALS['dat_enermy_scope'] = 
            setScopeEnermyFirstAttackTrue($n = 10, $file, $player);
        }
        changeScopeEnermyWhenMove($n = 10, $file = "data.txt", $player = 2);

        $enermy_index = $player % 2 + 1;
        $area = initArea();
        $direction_enermy = directionGo($file, $enermy_index);
        $limit_area = limitAreaEnermy($area, $direction_enermy);

        $arr_posible = [];
        for ($x = 0; $x < $n; $x++) {
            for ($y = 0; $y < $n; $y++) {
                if ($limit_area[$x][$y] == 1 && ($GLOBALS['dat_enermy_scope'][$x][$y] == 1)) {
                    $arr_posible[] = (object) ['x' => $x, 'y' => $y];
                }
            }
        }

        if ($arr_posible) {
            $rand = rand(0, count($arr_posible));
            $GLOBALS['dat_enermy_scope'][$arr_posible[$rand]->x][$arr_posible[$rand]->y] = 0;
            return "A {$arr_posible[$rand]->x} {$arr_posible[$rand]->y}";
        } else {
            return fullAttack();
        }
    }
}

//Trick
function checkNoOptionGlobal($n = 0) {
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($GLOBALS[$i][$j] == 1) {
                return false;
            }
        }
    }
    return true;
}

//Sumary=================================
function solveDat($file = "data.txt", $player = 2, $n = 10) {
    return fullAttack($file, $player, $n);
    if ($n == 10) {
        $GLOBALS['arr_op'] = [
            (object) ['status' => 1, 'x' => 2, 'y' => 2],
            (object) ['status' => 1, 'x' => 2, 'y' => 8],
            (object) ['status' => 1, 'x' => 5, 'y' => 2],
            (object) ['status' => 1, 'x' => 5, 'y' => 8],
            (object) ['status' => 1, 'x' => 7, 'y' => 2],
            (object) ['status' => 1, 'x' => 7, 'y' => 8]
        ];
    }

    return sinceAttackTrue($n, $file, $player);
}
