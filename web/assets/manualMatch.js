var ManualMatch = function (target) {
    this.bind();
};

ManualMatch.prototype = {
    bind: function() {
        var form = new MatchForm($('#manual-match'));
        form.render()
    }
};