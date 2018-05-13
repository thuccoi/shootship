<?php
include 'war.php';


$game = new Game(3, $sizemap);
writeRandomConfig($game, $fileconfig1, $fileconfig2);

if (!loadConfig($game, $fileconfig1, $fileconfig2)) {
    echo 'Cấu hình sai';
    exit;
}

war($game, $sizemap, $filedata, $fileconfig1, $fileconfig2);


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


<div id="js-draw"></div>
<button id="js-replay" onclick="Play.wreplay('#js-draw');">Xem lại</button>
<button id="js-pause" onclick="Play.pause();">Tạm dừng</button>
<button id="js-continue" onclick="Play.continue('#js-draw');">Tiếp tục</button>
<div style="float: left;">
    <div style="background: #2196F3;width: 50px;height: 50px; "></div> Tàu 1
</div>
<div style="float: left;">
    <div style="background: #4CAF50;width: 50px;height: 50px;"></div> Tàu 2
</div>
<script>
    var speed = <?= $speed ?>;
    var maps = <?= json_encode($maps) ?>;
    var ship1s = <?= json_encode($ship1s) ?>;
    var ship2s = <?= json_encode($ship2s) ?>;

    Play.replay('#js-draw');
</script>

<div id="js-stats" class="stats" style="display: none;">
    <?php
    stats($game, $filedata, $fileconfig1, $fileconfig2);
    ?>
</div>

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
        <a style="padding: 10px;" target="_blank" href="/ship/replay.php?time=<?= $time ?>&speed=10" ><?= $time ?></a>
        <?php
    }
    ?>
</div>