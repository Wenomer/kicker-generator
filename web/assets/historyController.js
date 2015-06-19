var HistoryController = function () {
    this.bind();
    console.log(2);
};

HistoryController.prototype = {
    bind: function () {
        $('.highlight-player').on('change', 'select', function(e){
            var player = $(e.currentTarget).val();
            $('table tr').removeClass('success').removeClass('danger');
            var tds = $('table').find('td[player="' + player + '"]');

            _.each(tds, function (td) {
                td = $(td);
                var tr = td.closest('tr');

                if (tr.find('.' + td.data('command')).data('score') == 5) {
                    tr.addClass('success');
                } else {
                    tr.addClass('danger');
                }
            });
        });
    }
};