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
}
?>

<div id="js-draw"></div>
<button onclick="Play.replay('#js-draw');">Xem lại</button>
<script>
    var maps = <?= json_encode($maps) ?>;
</script>

<?php
stats($game);
?>