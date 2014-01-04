<?php

$filename = '/etc/thermostat/temp.log';

function get_log_data($datatype) {
	$csv = array();
	$lines = file('/etc/thermostat/temp.log', FILE_IGNORE_NEW_LINES);
	
	foreach ($lines as $key => $value) {
	    $csv[$key] = str_getcsv($value);
	}
	
	$temps = array();
	$i = 0;
	foreach ($csv as $datasegment) {
		if ( $datasegment[0] != "32.0" ) {
			$temps[$i] = $datasegment[0];
			$i++;
		}
	}
	
	echo implode(",", $temps);
}

function get_log_start_time() {

	$csv = array();
	$lines = file('/etc/thermostat/temp.log', FILE_IGNORE_NEW_LINES);
	
	foreach ($lines as $key => $value) {
	    $csv[$key] = str_getcsv($value);
	}

	echo $csv[0][1] . "," . $csv[0][2] . "," . $csv[0][3] . "," . $csv[0][4] . "," . $csv[0][5];
}

?>

<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
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
    

		</script>
</head>
<?php

function toggle_light($socket) {
	if ($socket == 'all') {
		exec('sudo /home/pathartl/lights/lights all');
	} else {
		exec('sudo /home/pathartl/lights/lights toggle ' . $socket);
	}
}

function toggle_allLights($state) {
	exec('sudo ./home/pathartl/lights/lights all');
}

switch ($_POST['socket']) {
	// TUrn all the lights off
	case 'alloff':
		exec('sudo /home/pathartl/lights/lights off');
		break;
	// Turn all the lights on
	case 'allon':
		exec('sudo /home/pathartl/lights/lights on');
		break;
	// Toggle the lights based on the status of the majority
	case 'toggle':
		exec('sudo /home/pathartl/lights/lights all');
		break;
	// case 'sexytime':
	// 	toggle_light(2);
	// 	toggle_light(4);
	// 	toggle_light(1);
	// 	break;
	// case 'tv':
	// 	toggle_light(6);
	// 	break;
	// case 'reset':
	// 	reset_status();
	default:
		exec('sudo /home/pathartl/lights/lights toggle' . $_POST['socket']);
}

if (file_exists("/etc/lights/0")) {
	$light_status = "off";
	$op_light_status = "on";
} else {
	$light_status = "on";
	$op_light_status = "off";
}

?>

<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />


<meta name="viewport" content="width=device-width" />

<style>
	body {background: url(bg.png) repeat}
	input#lights {
		display: block;
		width: 68px;
		height: 240px;
		background: url(<?php echo $light_status; ?>.png);
		border: none;
		text-indent: -999em;
		-webkit-tap-highlight-color:  rgba(255, 255, 255, 0); 
		position: absolute;
		right: 30px;
		top: 40px;
	}
	input:focus { border: none; outline: 0; background: none;}
	input#lights:active, input#lights:hover {
		background: url(<?php echo $op_light_status; ?>.png)
	}

	#container {
		margin-right: 100px;
	}

	#cur-temp {
		color: white;
		font-size: 80px;
		font-family: Helvetica, Arial, sans-serif;
		margin-left: 30px;
	}
/*	@media screen and (max-width: 600px) {
		#container {
			width: 600px;
			height: 400px;
		}
	}*/
	@media screen and (max-width: 600px) {
		#container {
			width: 300px;
			height: 200px;
		}
	}
</style>

<script src="highcharts/js/highcharts.js"></script>
<script src="highcharts/js/modules/exporting.js"></script>

<div id="cur-temp"><?php echo exec("/usr/bin/temp"); ?>&deg;F</div>

<div id="container" ></div>

<form action="index.php" method="post">
<!-- <input type="submit" name="socket" value="1">
<input type="submit" name="socket" value="2">
<input type="submit" name="socket" value="3">
<input type="submit" name="socket" value="4">
<input type="submit" name="socket" value="5">
<input type="submit" name="socket" value="6">
<input type="submit" name="socket" value="alloff">
<input type="submit" name="socket" value="allon"> -->
<input id="lights" type="submit" name="socket" value="toggle">
</form>
