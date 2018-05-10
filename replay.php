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
<button onclick="replay(0);">Xem lại</button>
<script>
    var maps = <?= json_encode($maps) ?>;
    var i = 0;

    function replay() {

        var rp = setInterval(function () {
            
            if (play(i) === false) {
                clearInterval(rp);
            }
            i++;
        }, 100);
    }

    function play(i) {
        var length = maps.length;
        if (i < length) {
            $("#js-draw").html(maps[i]);
            return true;
        }
        return false;
    }

</script>

<?php
stats($game);
?>