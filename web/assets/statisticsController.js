var StatisticsController = function () {
    this.renderChart();
    this.loadMetrics();
};

StatisticsController.prototype = {
    loadMetrics: function () {
        var loader = new MetricsLoader();

        _.each($('.values-block'), function(block){
            loader.load($(block));
        });
    },

    renderChart: function () {
        $.getJSON('/api/statistics/rating-log', function (response) {
            var series = response.data;
            $('.rating-flow').highcharts({
                title: {
                    text: ''
                },
                yAxis: {
                    title: {
                        text: 'Rating'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                xAxis: {
                    title: {
                        text: 'Games Count'
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                },
                series: series
            });
        });
    }
};