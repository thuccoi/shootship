<?php
include 'war.php';


$game = new Game(3);

if (!loadConfig($game)) {
    echo 'Cấu hình sai';
    exit;
}

war($game);


$rpgame = new Game(3);
if (!loadConfig($rpgame)) {
    echo 'Cấu hình sai';
    exit;
}
$maps = replay($rpgame, "data.txt");
?>


<div id="js-draw"></div>
<button id="js-replay" onclick="Play.replay('#js-draw');">Xem lại</button>
<script>
    var maps = <?= json_encode($maps) ?>;
</script>

<?php
stats($game);
?>