var MetricsLoader = function () {};

MetricsLoader.prototype = {
    load: function(target) {
        $.getJSON(target.data('url'), [], function (response) {
            if (response.success) {
                target.find('.panel-body').text(response.data);
            }
        });
        var form = new MatchForm($('#manual-match'), false);
        form.render()
    }
};