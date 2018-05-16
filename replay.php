<?php
include 'war.php';


if (!isset($_GET['time']) || !isset($_GET['sizemap']) || !isset($_GET['speed'])) {
    echo '<a href="index.php">Hãy chọn cấu hình trước </a>';
    exit;
}
$speed = $_GET['speed'];
$time = $_GET['time'];
if ($time > 0) {

    $gtcf = getTimeConfig($time);

    $sizemap = $gtcf->sizemap;
    $uplay1 = $gtcf->uplay1;
    $uplay2 = $gtcf->uplay2;

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
    history($speed);
    ?>
</div>