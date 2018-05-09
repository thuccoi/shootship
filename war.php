<?php

include 'resource/game.php';
include 'solve.php';

/**
 * ship1 shoot x, y
 */
//$game->shipShoot1(7, 7);
/* ---------- */
/**
 * ship2 shoot x, y 
 */
//$game->shipShoot2(7, 7);
/* ---------- */
/**
 * move Top ship1 direction
 */
//$game->moveShip1("T");
/* ---------- */
/**
 * get index ship Won
 */
//echo $game->getIndexWon();
/* ---------- */
/**
 * check is Game Over
 */
//$game->isGameOver();
/* ---------- */

/**
 * draw Game  x, y, index
 */
//echo $game->Draw(3, 5, 2);
/* ---------- */



function toArray($line) {
    return array_map('trim', array_filter(explode(' ', $line)));
}

function getData($file = "data.txt") {
    $inputfile = [];
    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $cv = array_map('trim', array_filter(explode(' ', $line)));
            $arr = [];
            foreach ($cv as $v) {
                if ($v) {
                    $arr[] = $v;
                }
            }
            $inputfile[] = $arr;
        }
    }

    fclose($handle);
    return $inputfile;
}

function getConfigFile($file = "config1.txt") {
    $handle = fopen($file, "r");
    $listconfig = [];
    if ($handle) {
        $i = 0;
        while (($line = fgets($handle)) !== false) {
            if ($i == 3) {
                break;
            }

            $cv = toArray($line);
            if (count($cv) < 6) {
                echo "Sai file cấu hình";
                exit;
            }

            $listconfig[] = $cv;
            $i++;
        }
    }

    fclose($handle);

    return $listconfig;
}

function loadConfig($game) {
    $cf1s = getConfigFile("config1.txt");
    $cf2s = getConfigFile("config2.txt");

    foreach ($cf1s as $cf1) {

        $cell11 = new Cell($cf1[0], $cf1[1], 0);
        $cell12 = new Cell($cf1[2], $cf1[3], 0);
        $cell13 = new Cell($cf1[4], $cf1[5], 0);
        $config1 = [$cell11, $cell12, $cell13];
        foreach ($cf2s as $cf2) {
            $cell21 = new Cell($cf2[0], $cf2[1], 0);
            $cell22 = new Cell($cf2[2], $cf2[3], 0);
            $cell23 = new Cell($cf2[4], $cf2[5], 0);
            $config2 = [$cell21, $cell22, $cell23];

            if ($game->setConfig($config1, $config2)) {
                return TRUE;
            }
        }
    }

    return FALSE;
}

function writeAttack($obj, $x, $y, $status = 'F') {
    $data = "{$obj} A {$x} {$y} {$status}" . PHP_EOL;
    $handle = fopen('data.txt', 'a');
    fwrite($handle, $data);
    fclose($handle);
}

function writeDefense($obj, $dir) {
    $data = "{$obj} D {$dir}" . PHP_EOL;
    $handle = fopen('data.txt', 'a');
    fwrite($handle, $data);
    fclose($handle);
}

function emptyFile() {
    $handle = fopen('data.txt', 'w');

    fclose($handle);
}

function detailFile($arr) {
    foreach ($arr as $line) {
        foreach ($line as $v) {
            echo "$v ";
        }
        echo "\n";
    }
}

function war($game) {
    emptyFile();
    for ($i = 0; $i < 3000; $i++) {
        $solve1 = toArray(solve1());

        if (isset($solve1[0])) {
            if ($solve1[0] == 'A') {
                if (isset($solve1[1]) && isset($solve1[2])) {
                    $status = 'F';

                    if ($game->shipShoot1($solve1[1], $solve1[2]) == TRUE) {
                        $status = 'T';
                    }
                    writeAttack(1, $solve1[1], $solve1[2], $status);
                }
            } elseif ($solve1[0] == 'D') {
                if (isset($solve1[1])) {
                    if ($game->moveShip1($solve1[1])) {
                        writeDefense(1, $solve1[1]);
                    }
                }
            }
        }

        if ($game->isGameOver()) {
            break;
        }

        $solve2 = toArray(solve2());

        if (isset($solve2[0])) {
            if ($solve2[0] == 'A') {
                if (isset($solve2[1]) && isset($solve2[2])) {
                    $status = 'F';

                    if ($game->shipShoot2($solve2[1], $solve2[2]) == TRUE) {
                        $status = 'T';
                    }
                    writeAttack(2, $solve2[1], $solve2[2], $status);
                }
            } elseif ($solve2[0] == 'D') {
                if (isset($solve2[1])) {
                    if ($game->moveShip2($solve2[1])) {
                        writeDefense(2, $solve2[1]);
                    }
                }
            }
        }


        if ($game->isGameOver()) {
            break;
        }
    }
}

$game = new Game(3);

if (!loadConfig($game)) {
    echo 'Cấu hình sai';
    exit;
}

war($game);

echo "Tàu chiến thắng là tàu: " . $game->getIndexWon();
echo $game->Draw();

echo "<pre>";
echo "config1.txt\n\n";
detailFile(getData("config1.txt"));
echo "\n\n\n";
echo "config2.txt\n\n";
detailFile(getData("config2.txt"));
echo "\n\n\n";
echo "data.txt\n\n";
detailFile(getData());

