<?php
include 'war.php';


if (!isset($_GET['time']) || !isset($_GET['sizemap']) || !isset($_GET['speed'])) {
    echo '<a href="/ship">Hãy chọn cấu hình trước </a>';
    exit;
}

$time = $_GET['time'];
if ($time > 0) {
    $handle = fopen("history/{$time}/sizemap.txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $sizemap = (int) $line;
        }
        fclose($handle);
    }

    $handle = fopen("history/{$time}/nameship1.txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $uplay1 = trim($line);
        }
        fclose($handle);
    }

    $handle = fopen("history/{$time}/nameship2.txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $uplay2 = trim($line);
        }
        fclose($handle);
    }

    $game = new Game(3, $sizemap);

    if (!loadConfig($game, $fileconfig1, $fileconfig2, "history/{$time}/config.txt")) {
        echo 'Cấu hình sai';
        exit;
    }


    $replay = replay($game, "history/{$time}/data.txt");
    $maps = $replay->map;
    $ship1s = $replay->ship1;
    $ship2s = $replay->ship2;
} else {
    echo "Hãy nhập vào thời gian đã đấu: ?time=xxx";
    exit;
}
?>

<div id="js-draw"></div>
<button id="js-replay" onclick="Play.wreplay('#js-draw');">Xem lại</button>
<button id="js-pause" onclick="Play.pause();">Tạm dừng</button>
<button id="js-continue" onclick="Play.continue('#js-draw');">Tiếp tục</button>
<div style="float: left; padding-right: 10px;">
    <div style="background: #2196F3;width: 50px;height: 50px; "></div> <?= shipName($uplay1) ?>
</div>
<div style="float: left; padding-right: 10px;">
    <div style="background: #4CAF50;width: 50px;height: 50px;"></div> <?= shipName($uplay2) ?>
</div>

<script>
    var speed = <?= $speed ?>;
    var sizemap = <?= $sizemap ?>;
    var maps = <?= json_encode($maps) ?>;
    var ship1s = <?= json_encode($ship1s) ?>;
    var ship2s = <?= json_encode($ship2s) ?>;
</script>

<div id="js-stats" class="stats" >
    <?php
    stats($game, $filedata, $fileconfig1, $fileconfig2, $time, TRUE, $uplay1, $uplay2);
    ?>
</div>
<div style="clear: both"></div>
<div class="dirs" style="float: left; width: 500px;">
    <?php
    $dirs = array_filter(glob('history/*'), 'is_dir');
    $times = [];
    foreach ($dirs as $val) {
        $time = str_replace('history/', '', $val);
        $times[] = $time;
    }
    $times = array_reverse($times);

    foreach ($times as $time) {
        ?>
        <a style="padding: 10px;" target="_blank" href="/ship/replay.php?time=<?= $time ?>&sizemap=<?= $sizemap ?>&speed=<?= $speed ?>"><?= $time ?></a>
        <?php
    }
    ?>
</div>