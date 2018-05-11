var Play = new function __Play() {
    this.isPlay = false;
    this.rp = null;
    this.idx = 0;
    this.replay = function (canvas) {
        if (Play.isPlay == false) {
            $("#js-replay").hide();
            Play.isPlay = true;
            Play.rp = setInterval(function () {
                if (play(Play.idx, canvas) == false) {
                    Play.pause();
                    $("#js-replay").show();
                    $("#js-stats").show();
                    Play.isPlay = false;
                }
                Play.idx++;
            }, speed);
        }
    };

    this.pause = function (i) {
        console.log(ship1s[Play.idx - 1]);
        console.log(ship2s[Play.idx - 1]);
        clearInterval(Play.rp);
    };

    this.continue = function (canvas) {
        Play.isPlay = false;
        Play.replay(canvas);
    };

    function play(i, canvas) {
        var length = maps.length;
        if (i < length) {
            $(canvas).html(maps[i]);
            return true;
        }
        return false;
    }
};