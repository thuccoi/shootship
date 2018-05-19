<?php

    require_once 'PP.php';

    function solvePhuong($file = "data.txt", $player = 1, $sizemap = 10) {

        $pp = new \PP($file, $player, $sizemap);
        $pp->setTHMode();

        $data = file($file);
        $lines = count($data) - 1;

        if ($lines > 0) {
            $move = $pp->solve();
        } else {
            $move = $pp->fireRandom();
        }

        return $move;
    }
    