<?php

require_once 'PP.php';

function solvePhuong($file = "data.txt", $player = 1, $sizemap = 10) {

    $pp = new \PP($file);

    if (filesize($file)) {

        $last = $pp->getLastLine();

        $get = $pp->get();
        $rget = array_reverse($get);

        if ($last) {
            $getLine = explode(' ', $last);
            if ($getLine[0] != $player) {
                if ($getLine[1] == 'A') {
                    if (strpos($getLine[4], 'T') !== false) {
                        return \PP::runRandom();
                    }
                }
            }

            for ($iA2 = 0; $iA2 < count($rget); $iA2++) {
                $arrayOfAction = explode(' ', $rget[$iA2]);

                if ($arrayOfAction) {

                    if ($arrayOfAction[0] == $player) {
                        if ($arrayOfAction[1] == 'A') {
                            $lastEl = array_values(array_slice($arrayOfAction, -1))[0];

                            if (strpos($lastEl, 'T') !== false) {
                                $y = $arrayOfAction[2];
                                $x = $arrayOfAction[3];

                                $ya = $y - 1;
                                $yp = $y + 1;
                                $xa = $x - 1;
                                $xp = $x + 1;

                                $array = [
                                    "A {$yp} {$x}",
                                    "A {$ya} {$x}",
                                    "A {$yp} {$xp}",
                                    "A {$yp} {$xa}",
                                    "A {$y} {$xa}",
                                    "A {$y} {$xp}",
                                    "A {$ya} {$xp}",
                                    "A {$ya} {$xa}",
                                ];

                                return $array[array_rand($array)];
                            }
                        }
                    }
                }
            }
        }
    }
    $move = $pp->fireRandom();
    return $move;
}
