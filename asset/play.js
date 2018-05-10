var Play = new function __Play() {

    this.replay = function (canvas) {
        var i = 0;
        var rp = setInterval(function () {

            if (play(i, canvas) === false) {
                clearInterval(rp);
            }
            i++;
        }, 100);
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