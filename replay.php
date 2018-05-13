<?php
include 'war.php';

$time = $_GET['time'];
if ($time > 0) {
    $game = new Game(3, $sizemap);

    if (!loadConfig($game, "history/config_{$time}.txt")) {
        echo 'Cấu hình sai';
        exit;
    }


    $replay = replay($game, "history/data_{$time}.txt");
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

<script>
    var speed = <?= $speed ?>;
    var maps = <?= json_encode($maps) ?>;
    var ship1s = <?= json_encode($ship1s) ?>;
    var ship2s = <?= json_encode($ship2s) ?>;
</script>

<div id="js-stats" class="stats" >
    <?php
    stats($game, $time, TRUE);
    ?>
</div>