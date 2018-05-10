<?php
include 'war.php';

$time = $_GET['time'];
if ($time > 0) {
    $game = new Game(3);

    if (!loadConfig($game, "history/config_{$time}.txt")) {
        echo 'Cấu hình sai';
        exit;
    }


    $maps = replay($game, "history/data_{$time}.txt");
} else {
    echo "Hãy nhập vào thời gian đã đấu: ?time=xxx";
    exit;
}
?>

<div id="js-draw"></div>
<button id="js-replay" onclick="Play.replay('#js-draw');">Xem lại</button>
<script>
    var maps = <?= json_encode($maps) ?>;
</script>

<div id="js-stats" class="stats" >
    <?php
    stats($game);
    ?>
</div>