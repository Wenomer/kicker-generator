var HistoryController = function () {
    this.bind();
};

HistoryController.prototype = {
    bind: function () {
        $('.highlight-player').on('change', 'select', function(e){
            var player = $(e.currentTarget).val();
            $('h4 .day-change').removeClass('rating-positive').removeClass('rating-negative').html('');
            $('table tr').removeClass('success').removeClass('danger');
            var tds = $('table').find('td[player="' + player + '"]');
            var totalDayChange = {};

            _.each(tds, function (td) {
                td = $(td);

                if (_.isUndefined(totalDayChange[td.closest('table').data('day')])) {
                    totalDayChange[td.closest('table').data('day')] = 0;
                }

                totalDayChange[td.closest('table').data('day')] += parseFloat(td.data('rating-change'));

                var tr = td.closest('tr');

                if (tr.find('.' + td.data('command')).data('score') == 5) {
                    tr.addClass('success');
                } else {
                    tr.addClass('danger');
                }
            });

            var dayChange;
            _.each(totalDayChange, function(value, index){
                dayChange = $('h4[title-day="' + index + '"] .day-change');
                dayChange.html('(' + Math.round(value * 100) / 100 + ')');
                dayChange.addClass(value > 0 ? 'rating-positive' : 'rating-negative');
            });
        });
    }
};