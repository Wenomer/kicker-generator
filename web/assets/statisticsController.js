var StatisticsController = function () {
    this.renderChart();
};

StatisticsController.prototype = {
    renderChart: function () {
        $.getJSON('/api/statistics/rating-log', function (series) {
            console.log(series);
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
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: series
            });
        });
    }
};