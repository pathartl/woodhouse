<script>
    var tempChart = $(function () {
        $('#container').highcharts({
            chart: {
            	backgroundColor:'rgba(255, 255, 255, 0.0001)',
                type: 'spline'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 20,
                labels: {
                	step: 4,
                	staggerLines: 1
                }
            },
            yAxis: {
                minorGridLineWidth: 0,
                gridLineWidth: 0,
                alternateGridColor: null,
                title: ""
            },
            tooltip: {
                valueSuffix: '\xB0F'
            },
            legend: {
            	enabled: false
            },
            exporting: {
         		enabled: false
    		},
    		credits: {
    			enabled: false
    		},
    		title: {
    			text: ""
    		},
            plotOptions: {
                spline: {
                    lineWidth: 2,
                    states: {
                        hover: {
                            lineWidth: 3
                        }
                    },
                    marker: {
                        enabled: false
                    },
                    pointInterval: 300000, // ten minutes
                    pointStart: Date.UTC(<?php get_log_start_time(); ?>, 0)
                }
            },
            series: [{
                color: '#33B5E5',
                name: 'Room Temperature',
                data: [<?php get_log_data(); ?>]
        
            }]
            ,
            navigation: {
                menuItemStyle: {
                    fontSize: '10px'
                }
            }
        });
    });
    setInterval(mediaIDTimer, 60000);
</script>