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
    $line = str_replace('0', '*', $line);
    $data = array_map('trim', array_filter(explode(' ', $line)));
    $rs = [];
    foreach ($data as $val) {
        if ($val == '*') {
            $val = 0;
        }
        $rs[] = $val;
    }
    return $rs;
}

function getData($file = "data.txt") {
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
        fclose($handle);
    }


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

function randomPosition($game) {
    $pos = [
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 1], (object) ['x' => 2, 'y' => 2]],
        [(object) ['x' => 0, 'y' => 2], (object) ['x' => 1, 'y' => 1], (object) ['x' => 2, 'y' => 0]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 0], (object) ['x' => 0, 'y' => 1]],
        [(object) ['x' => 1, 'y' => 0], (object) ['x' => 0, 'y' => 1], (object) ['x' => 1, 'y' => 1]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 0, 'y' => 1], (object) ['x' => 1, 'y' => 1]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 0], (object) ['x' => 1, 'y' => 1]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 0, 'y' => 1], (object) ['x' => 1, 'y' => 2]],
        [(object) ['x' => 1, 'y' => 0], (object) ['x' => 1, 'y' => 1], (object) ['x' => 0, 'y' => 2]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 1], (object) ['x' => 0, 'y' => 2]],
        [(object) ['x' => 1, 'y' => 0], (object) ['x' => 0, 'y' => 1], (object) ['x' => 1, 'y' => 2]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 1], (object) ['x' => 2, 'y' => 0]],
        [(object) ['x' => 0, 'y' => 1], (object) ['x' => 1, 'y' => 0], (object) ['x' => 2, 'y' => 1]],
        [(object) ['x' => 0, 'y' => 1], (object) ['x' => 0, 'y' => 1], (object) ['x' => 0, 'y' => 2]],
        [(object) ['x' => 0, 'y' => 0], (object) ['x' => 1, 'y' => 0], (object) ['x' => 2, 'y' => 0]]
    ];


    $pp = rand(0, 213213223) % (($game->sizemap - 3) * ($game->sizemap - 3));

    $x = (int) ($pp / $game->sizemap);
    $y = $pp - $x * $game->sizemap;

    $npos = [];
    foreach ($pos as $arr) {
        $line = [];
        foreach ($arr as $val) {
            $val->x += $x;
            $val->y += $y;

            $line[] = $val->x;
            $line[] = $val->y;
        }

        $npos[] = $line;
    }

    $rp = rand(0, 1243434344) % count($npos);

    return implode(" ", $npos[$rp]).' ';
}

function writeRandomConfig($game) {
    $line1 = randomPosition($game) . PHP_EOL;
    $line2 = randomPosition($game) . PHP_EOL;
    $line3 = randomPosition($game) . PHP_EOL;

    $handle = fopen('e3822af53cf86a768888349b90cf480c_864299_CHG5J82EZUSQ4.txt', 'w');
    fwrite($handle, $line1);
    fwrite($handle, $line2);
    fwrite($handle, $line3);
    fclose($handle);

    $line1 = randomPosition($game) . PHP_EOL;
    $line2 = randomPosition($game) . PHP_EOL;
    $line3 = randomPosition($game) . PHP_EOL;


    $handle = fopen('6dbc1e2066279f11fdbe3184b6145678_352551_UTDUFPTWN3FPZ.txt', 'w');
    fwrite($handle, $line1);
    fwrite($handle, $line2);
    fwrite($handle, $line3);
    fclose($handle);
}

function loadConfig($game, $replayfile = FALSE) {
    if ($replayfile) {
        $cf = getConfigFile($replayfile);
        $cf1 = $cf[0];
        $cf2 = $cf[1];

        $cell11 = new Cell($cf1[0], $cf1[1], 0);
        $cell12 = new Cell($cf1[2], $cf1[3], 0);
        $cell13 = new Cell($cf1[4], $cf1[5], 0);
        $config1 = [$cell11, $cell12, $cell13];

        $cell21 = new Cell($cf2[0], $cf2[1], 0);
        $cell22 = new Cell($cf2[2], $cf2[3], 0);
        $cell23 = new Cell($cf2[4], $cf2[5], 0);
        $config2 = [$cell21, $cell22, $cell23];

        if ($game->setConfig($config1, $config2)) {
            return TRUE;
        }

        return FALSE;
    } else {
        $cf1s = getConfigFile("e3822af53cf86a768888349b90cf480c_864299_CHG5J82EZUSQ4.txt");
        $cf2s = getConfigFile("6dbc1e2066279f11fdbe3184b6145678_352551_UTDUFPTWN3FPZ.txt");

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
    }
    return FALSE;
}

function writeAttack($obj, $x, $y, $status = 'F') {
    $data = "{$obj} A {$x} {$y} {$status}" . PHP_EOL;
    $data = str_replace('*', '', $data);
    $handle = fopen('data.txt', 'a');
    fwrite($handle, $data);
    fclose($handle);
}

function writeDefense($obj, $dir) {
    $data = "{$obj} D {$dir}" . PHP_EOL;
    $data = str_replace('*', '', $data);
    $handle = fopen('data.txt', 'a');
    fwrite($handle, $data);
    fclose($handle);
}

function emptyFile($game) {
    $time = time();
    copy('data.txt', 'history/data_' . $time . '.txt');

    $config1 = $game->ship1->getTextConfig() . PHP_EOL;
    $config2 = $game->ship2->getTextConfig() . PHP_EOL;

    $hd = fopen('history/config_' . $time . '.txt', 'w');
    fwrite($hd, $config1);
    fwrite($hd, $config2);

    fclose($hd);

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
    emptyFile($game);

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

function replay($game, $datafile = 'data.txt') {
    $data = getData($datafile);

    $maps = [$game->Draw()];
    $ship1s = [$game->ship1->getConfig()];
    $ship2s = [$game->ship2->getConfig()];
    foreach ($data as $line) {
        if (isset($line[0]) && isset($line[1])) {
            if ($line[1] == 'A') {

                if (isset($line[2]) && isset($line[3])) {
                    if ($line[0] == 1) {
                        $game->shipShoot1($line[2], $line[3]);
                    } elseif ($line[0] == 2) {
                        $game->shipShoot2($line[2], $line[3]);
                    }
                }

                $maps[] = $game->Draw($line[2], $line[3], $line[0]);
                $ship1s[] = $game->ship1->getConfig();
                $ship2s[] = $game->ship2->getConfig();
            } elseif ($line[1] == 'D') {
                if (isset($line[2])) {
                    if ($line[0] == 1) {
                        $game->moveShip1($line[2]);
                    } elseif ($line[0] == 2) {
                        $game->moveShip2($line[2]);
                    }
                }
                $maps[] = $game->Draw();
                $ship1s[] = $game->ship1->getConfig();
                $ship2s[] = $game->ship2->getConfig();
            }
        }
    }

    return (object) [
                "map" => $maps,
                "ship1" => $ship1s,
                "ship2" => $ship2s
    ];
}

function stats($game, $time = FALSE, $replay = FALSE) {

    echo "Tàu chiến thắng là tàu: " . $game->getIndexWon();
    // echo $game->Draw();

    $inputdata = getData();
    echo "<p>Tổng số bước là: " . count($inputdata) . " </p>";
    $nums = array_reduce($inputdata, function($sum, $e) {
        if ($e[0] == 1 && $e[1] == 'A') {
            $sum->ship1_A ++;
        } elseif ($e[0] == 2 && $e[1] == 'A') {
            $sum->ship2_A++;
        }

        if ($e[0] == 1 && $e[1] == 'D') {
            $sum->ship1_D ++;
        } elseif ($e[0] == 2 && $e[1] == 'D') {
            $sum->ship2_D++;
        }


        return $sum;
    }, (object) ['ship1_A' => 0, 'ship2_A' => 0, 'ship1_D' => 0, 'ship2_D' => 0]);

    echo "<p>Tàu 1 bắn hết: {$nums->ship1_A} phát</p>";
    echo "<p>Tàu 2 bắn hết: {$nums->ship2_A} phát</p>";


    echo "<p>Tàu 1 chạy: {$nums->ship1_D} bước</p>";
    echo "<p>Tàu 2 chạy: {$nums->ship2_D} bước</p>";

    echo "<pre>";
    if ($replay == FALSE) {
        echo "config1.txt\n\n";
        detailFile(getData("6dbc1e2066279f11fdbe3184b6145678_352551_UTDUFPTWN3FPZ.txt"));
        echo "\n\n\n";
        echo "config2.txt\n\n";
        detailFile(getData("e3822af53cf86a768888349b90cf480c_864299_CHG5J82EZUSQ4.txt"));

        echo "\n\n\n";
        echo "data.txt\n\n";
        detailFile(getData());
    } else {
        echo "config.txt\n\n";
        detailFile(getData("history/config_{$time}.txt"));
        echo "\n\n\n";
        echo "data.txt\n\n";
        detailFile(getData("history/data_{$time}.txt"));
    }
    echo "</pre>";
}
?>

<link rel="stylesheet" href="asset/style.css">
<script src="asset/jquery-3.3.1.min.js"></script>
<script src="asset/play.js"></script>