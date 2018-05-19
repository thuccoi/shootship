<?php
function solveHardy($file_data = "data.txt", $player, $sizemap = 10) {
    $allSteps = [];
    $board = [];    // nxn array
    

    $hf_vals = (object) [
        "const_SIZE" => $sizemap,    // should we take it as parameter?!
        "const_MOVEUP" => 1,
        "const_MOVEDOWN" => 2,
        "const_MOVELEFT" => 4,
        "const_MOVERIGHT" => 8,
        "my_order" => $player,
        "const_DAMAGE" => 200,
        "stscore" => 0,   // strategic score
        "mc" => 3.1,  // mine
        "oc" => 3,  // opposite
        "cc" => 1,  // common
        "mh" => 0,  // my hits count
        "oh" => 0,   // opposite hits count
        "redflags" => [],
        "blackflags" => [],
        "myshots" => [],
        "theirshots" => []
    ];

    $hf_init = function(&$board, &$hf_vals) {
        $ssize = $hf_vals->const_SIZE * $hf_vals->const_SIZE;
        $board = array_fill(0, $ssize, 10);
    };

    $hf_readData = function($file, &$allSteps, &$hf_vals) {
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $row = explode(' ', trim($line));
                if ($row[1] == "A" || $row[1] == "a") {
                    $move = new StdClass();
                    $move->attack = 1;
                    $move->x = $row[2];
                    $move->y = $row[3];
                    $move->on_target = ($row[4] == "T" || $row[4] == "t") ? 1 : 0;
                } else {
                    $move = new StdClass();
                    $move->attack = 0;
                    if ($row[2] == "T" || $row[2] == "t") {
                        $move->direction = $hf_vals->const_MOVEUP;
                    } else if ($row[2] == "B" || $row[2] == "b") {
                        $move->direction = $hf_vals->const_MOVEDOWN;
                    } else if ($row[2] == "L" || $row[2] == "l") {
                        $move->direction = $hf_vals->const_MOVELEFT;
                    } else {
                        $move->direction = $hf_vals->const_MOVERIGHT;
                    }
                }

                $move->player = $row[0];
                $allSteps[] = $move;
            }

            fclose($handle);
        } else {
            // error opening the file.
        } 
    };

    $hf_getNeighbors = function($idx) use ($hf_vals) {
        $ssize = $hf_vals->const_SIZE * $hf_vals->const_SIZE;
        $r = [];
        $y = (int) $idx % $hf_vals->const_SIZE;
        $x = (int) (($idx - $y) / $hf_vals->const_SIZE);
        
        if ($x > 0 && $y > 0) {
            $idx_1 = $idx - $hf_vals->const_SIZE - 1;
            if ($idx_1 >= 0) {
                $r[] = $idx_1;
            }
        }

        if ($y > 0) {
            $idx_2 = $idx - 1;
            if ($idx_2 >= 0) {
                $r[] = $idx_2;
            }
        }

        if ($x < 9 && $y > 0) {
            $idx_3 = $idx + $hf_vals->const_SIZE - 1;
            if ($idx_3 >= 0) {
                $r[] = $idx_3;
            }
        }

        if ($x > 0) {
            $idx_4 = $idx - $hf_vals->const_SIZE;
            if ($idx_4 >= 0) {
                $r[] = $idx_4;
            }
        }

        if ($x < 9) {
            $idx_5 = $idx + $hf_vals->const_SIZE;
            if ($idx_5 < $ssize) {
                $r[] = $idx_5;
            }
        }
        
        if ($y < 9 && $x > 0) {
            $idx_6 = $idx - $hf_vals->const_SIZE + 1;
            if ($idx_6 >= 0) {
                $r[] = $idx_6;
            }
        }

        if ($y < 9) {
            $idx_7 = $idx + 1;
            if ($idx_7 < $ssize) {
                $r[] = $idx_7;
            }
        }

        if ($x < 9 && $y < 9) {
            $idx_8 = $idx + $hf_vals->const_SIZE + 1;
            if ($idx_8 < $ssize) {
                $r[] = $idx_8;
            }
        }

        return $r;
    };

    $hf_retouchNeighbors = function($idx, &$board, $tval) use ($hf_vals, $hf_getNeighbors) {
        $ssize = $hf_vals->const_SIZE * $hf_vals->const_SIZE;
        $nbs = $hf_getNeighbors($idx);
        foreach ($nbs as $nb) {
            $board[$nb] += $tval;
        }
    };

    $hf_refineFlags = function($direction, &$flags, &$board, $change_score = false, $tune_neighbors = false) use($hf_vals, $hf_retouchNeighbors) {
        $ssize = $hf_vals->const_SIZE * $hf_vals->const_SIZE;
        switch ($direction) {
            case $hf_vals->const_MOVEUP:
                for ($i_r = 0; $i_r < count($flags); $i_r++) {
                    $old_val = $flags[$i_r];
                    $y = (int) $flags[$i_r] % $hf_vals->const_SIZE;
                    $x = (int) (($flags[$i_r] - $y) / $hf_vals->const_SIZE);
                    $y--;
                    $new_val = $x * $hf_vals->const_SIZE + $y;
                    if ($new_val >= 0) {
                        $flags[$i_r] = $new_val;
                        if ($tune_neighbors) {
                            $hf_retouchNeighbors($old_val, $board, -1);
                            $hf_retouchNeighbors($new_val, $board, 1);
                        }
                    }
                    
                    if ($change_score) {
                        $board[$old_val] -= $change_score;
                        if ($new_val >= 0) {
                            $board[$flags[$i_r]] += $change_score;
                        }
                    }
                }
                break;

            case $hf_vals->const_MOVEDOWN:
                for ($i_r = 0; $i_r < count($flags); $i_r++) {
                    $old_val = $flags[$i_r];
                    $y = (int) $flags[$i_r] % $hf_vals->const_SIZE;
                    $x = (int) (($flags[$i_r] - $y) / $hf_vals->const_SIZE);
                    $y++;
                    $new_val = $x * $hf_vals->const_SIZE + $y;
                    if ($new_val < $ssize) {
                        $flags[$i_r] = $new_val;
                        if ($tune_neighbors) {
                            $hf_retouchNeighbors($old_val, $board, -1);
                            $hf_retouchNeighbors($new_val, $board, 1);
                        }
                    }

                    if ($change_score) {
                        $board[$old_val] -= $change_score;
                        if ($new_val < $ssize) {
                            $board[$flags[$i_r]] += $change_score;
                        }
                    }
                }
                break;

            case $hf_vals->const_MOVELEFT:
                for ($i_r = 0; $i_r < count($flags); $i_r++) {
                    $old_val = $flags[$i_r];
                    $y = (int) $flags[$i_r] % $hf_vals->const_SIZE;
                    $x = (int) (($flags[$i_r] - $y) / $hf_vals->const_SIZE);
                    $x--;
                    $new_val = $x * $hf_vals->const_SIZE + $y;
                    if ($new_val >= 0) {
                        $flags[$i_r] = $new_val;
                        if ($tune_neighbors) {
                            $hf_retouchNeighbors($old_val, $board, -1);
                            $hf_retouchNeighbors($new_val, $board, 1);
                        }
                    }
                    if ($change_score) {
                        $board[$old_val] -= $change_score;
                        if ($new_val >= 0) {
                            $board[$flags[$i_r]] += $change_score;
                        }
                    }
                }
                break;

            case $hf_vals->const_MOVERIGHT:
                for ($i_r = 0; $i_r < count($flags); $i_r++) {
                    $old_val = $flags[$i_r];
                    $y = (int) $flags[$i_r] % $hf_vals->const_SIZE;
                    $x = (int) (($flags[$i_r] - $y) / $hf_vals->const_SIZE);
                    $x++;
                    $new_val = $x * $hf_vals->const_SIZE + $y;
                    if ($new_val < $ssize) {
                        $flags[$i_r] = $new_val;
                        if ($tune_neighbors) {
                            $hf_retouchNeighbors($old_val, $board, -1);
                            $hf_retouchNeighbors($new_val, $board, 1);
                        }
                    }
                    if ($change_score) {
                        $board[$old_val] -= $change_score;
                        if ($new_val < $ssize) {
                            $board[$flags[$i_r]] += $change_score;
                        }
                    }
                }
                break;
        }
    };

    $hf_analyze = function(&$allSteps, &$board, &$hf_vals) use($hf_refineFlags, $hf_retouchNeighbors, $hf_getNeighbors) {
        // $hf_vals->redflags = [];
        // $hf_vals->blackflags = [];
        $ssize = $hf_vals->const_SIZE * $hf_vals->const_SIZE;
        for ($i = 0; $i < count($allSteps); $i++) {
            $move = $allSteps[$i];
            if ($move->player == $hf_vals->my_order) {
                // it's my turn
                if ($move->attack) {
                    $idx = $move->x * $hf_vals->const_SIZE + $move->y;
                    $hf_vals->myshots[] = $idx;
    
                    if ($move->on_target == 1) {    // hooray, we hit them
                        $hf_vals->mh++;
                        // mark this cells damaged
                        $board[$idx] += $hf_vals->const_DAMAGE;
                        // also increase scores of nearby cells
                        $hf_retouchNeighbors($idx, $board, 1);
                        // save it
                        $hf_vals->redflags[] = $idx;
                    } else {
                        $board[$idx] -= 3;
                    }
                } else {
                    // keep running, man
                    // revise black flags
                    if (count($hf_vals->blackflags)) {
                        $hf_refineFlags($move->direction, $hf_vals->blackflags, $board, false, false);
                    }
                }
            } else {
                if ($move->attack) {
                    $idx = $move->x * $hf_vals->const_SIZE + $move->y;
                    $hf_vals->theirshots[] = $idx;
    
                    if ($move->on_target == 1) {    // fuck, we get hit right on the face
                        $hf_vals->oh++;
                        // mark this as water
                        $board[$idx] = 10;
                        // save it
                        $hf_vals->blackflags[] = $idx;
                    } else {
                        // simple ignore because they missed
                    }
                } else {
                    // revise red flags
                    if (count($hf_vals->redflags)) {
                        $hf_refineFlags($move->direction, $hf_vals->redflags, $board, $hf_vals->const_DAMAGE, true);
                    }
                    // also tune up my shots
                    $hf_refineFlags($move->direction, $hf_vals->myshots, $board, -3, false);
                }
            }

            // calculate strategic score
            $hf_vals->stscore += count($hf_vals->redflags) * ($hf_vals->mc * count($hf_vals->myshots)) / $ssize;
            $hf_vals->stscore -= count($hf_vals->blackflags) * ($hf_vals->oc * count($hf_vals->theirshots)) / $ssize;
            $hf_vals->stscore += $i/$ssize;
        }
    };
    
    $hf_makeDecision = function(&$board, &$allSteps) use ($hf_vals, $hf_getNeighbors) {
        if ($hf_vals->stscore >= 0) {
            // Attack them NOW!!!
            $idx = 0;
            if (count($hf_vals->redflags)) {
                // target locked on
                /*
                $hotPoints = array_filter($board, function($e) {
                    return ($e > 10 && $e < 20);
                });
                */
                $hotPoints = [];
                for ($r = 0; $r < count($hf_vals->redflags); $r++) {
                    $hotPoints = array_unique(array_merge($hotPoints, $hf_getNeighbors($hf_vals->redflags[$r])));
                }
                $hotScores = [];
                foreach ($hotPoints as $h) {
                    if ($board[$h] > 9 && $board[$h] < 100) {
                        $hotScores[$h] = $board[$h];
                    }
                }

                if (!count($hotScores)) {
                    $idx = array_rand($hotPoints);
                    /*
                    $allUncheckPoints = array_filter($board, function($e) {
                        return ($e > 9 && $e < 100);
                    });
                    if (!count($allUncheckPoints)) {
                        $idx = array_rand($board);
                    } else {
                        $idx = array_rand($allUncheckPoints);
                    }
                    */
                } else {
                    // $idx = array_rand($hotPoints);
                    $best_choices = array_keys($hotScores, max($hotScores));
                    $idx = $best_choices[0];
                }
            } else {
                // better random in all board;
                $allUncheckPoints = array_filter($board, function($e) {
                    return $e > 9;
                });
                if (!count($allUncheckPoints)) {
                    $idx = array_rand($board);
                } else {
                    $idx = array_rand($allUncheckPoints);
                }
            }

            $y = (int) $idx % $hf_vals->const_SIZE;
            $x = (int) (($idx - $y) / $hf_vals->const_SIZE);
            return "A {$x} {$y}";
        } else {
            // Run! Hardy, RUNNNN
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
    };

    $hf_init($board, $hf_vals);
    $hf_readData($file_data, $allSteps, $hf_vals);
    $hf_analyze($allSteps, $board, $hf_vals);
    return $hf_makeDecision($board, $allSteps);
}

?>