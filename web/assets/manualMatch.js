var ManualMatch = function () {
    this.bind();
};

ManualMatch.prototype = {
    bind: function() {
        var form = new MatchForm($('#manual-match'), false);
        form.render()
    }
};