var Play = new function __Play() {
    this.isPlay = false;

    this.replay = function (canvas) {
        if (this.isPlay == false) {
            $("#js-replay").hide();
            var i = 0;
            this.isPlay = true;
            var rp = setInterval(function () {
                if (play(i, canvas) === false) {
                    clearInterval(rp);
                    
                    $("#js-replay").show();
                    
                    $("#js-stats").show();
                    
                    Play.isPlay = false;
                }
                i++;
            }, 10);
        }
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