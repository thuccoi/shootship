var Play = new function __Play() {

    this.rp = null;
    this.idx = 0;

    this.wreplay = function (canvas) {
        this.idx = 0;
        this.replay(canvas);
    };

    this.replay = function (canvas) {


        $("#js-replay").hide();
        Play.rp = setInterval(function () {
            if (play(Play.idx, canvas) == false) {

                Play.pause();
                $("#js-replay").show();
                $("#js-stats").show();
            }
            Play.idx++;
        }, speed);


    };

    this.pause = function () {
        clearInterval(Play.rp);
    };

    this.continue = function (canvas) {

        Play.replay(canvas);
    };

    function play(i, canvas) {
        var length = maps.length;
        if (i < length) {
            $(canvas).html(maps[i]);
            var size = 550 / sizemap;
            $(".main .map .row .col").each(function () {
                $(this).width(size);
                $(this).height(size);
            });

            $(".main .map .row .fire .cell").each(function () {
                $(this).width(size);
                $(this).height(size);
                $(this).css({'line-height': size + 'px'});
            });
            return true;
        }
        return false;
    }
};