<?php
include 'war.php';

if (!isset($_GET['ship1']) || !isset($_GET['ship2']) || !isset($_GET['sizemap']) || !isset($_GET['speed'])) {
    echo '<a href="/ship">Hãy chọn cấu hình trước </a>';
    exit;
}
$game = new Game(3, $sizemap);
writeRandomConfig($game, $fileconfig1, $fileconfig2);

if (!loadConfig($game, $fileconfig1, $fileconfig2)) {
    echo 'Cấu hình sai';
    exit;
}

war($game, $sizemap, $filedata, $fileconfig1, $fileconfig2, $uplay1, $uplay2);

$rpgame = new Game(3, $sizemap);
if (!loadConfig($rpgame, $fileconfig1, $fileconfig2)) {
    echo 'Cấu hình sai';
    exit;
}

$replay = replay($rpgame, $filedata);
$maps = $replay->map;
$ship1s = $replay->ship1;
$ship2s = $replay->ship2;
?>


<div style="float: left;" id="js-draw"></div>
<div style="float: left;">
    <button id="js-replay" onclick="Play.wreplay('#js-draw');">Xem lại</button>
    <button id="js-pause" onclick="Play.pause();">Tạm dừng</button>
    <button id="js-continue" onclick="Play.continue('#js-draw');">Tiếp tục</button>
    <div style="float: left; padding-right: 10px;">
        <div style="background: #2196F3;width: 50px;height: 50px; "></div> <?= shipName($_GET['ship1']) ?>
    </div>
    <div style="float: left; padding-right: 10px;">
        <div style="background: #4CAF50;width: 50px;height: 50px;"></div> <?= shipName($_GET['ship2']) ?>
    </div>
</div>
<script>
    var speed = <?= $speed ?>;
    var sizemap = <?= $sizemap ?>;
    var maps = <?= json_encode($maps) ?>;
    var ship1s = <?= json_encode($ship1s) ?>;
    var ship2s = <?= json_encode($ship2s) ?>;

    Play.replay('#js-draw');
</script>

<div id="js-stats" class="stats" style="display: none;">
    <?php
    stats($game, $filedata, $fileconfig1, $fileconfig2, FALSE, FALSE, $uplay1, $uplay2);
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
        <a style="padding: 10px;" target="_blank" href="/ship/replay.php?time=<?= $time ?>&ship1=<?= $uplay1 ?>&ship2=<?= $uplay2 ?>&sizemap=<?= $sizemap ?>&speed=<?= $speed ?>" ><?= $time ?></a>
        <?php
    }
    ?>
</div>