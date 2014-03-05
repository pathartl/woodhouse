<?php

$filename = '/etc/thermostat/temp.log';
include 'config.php';
include 'lights.php';
include 'xbmc.php';

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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,300italic,300,100italic,100,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
	<script src="highcharts/js/highcharts.js"></script>
	<script src="highcharts/js/modules/exporting.js"></script>
	<script type="text/javascript" src="jquery.simpleWeather-2.5.min.js"></script>
	<script type="text/javascript" src="weather.js"></script>
	<?php include 'chart.php'; ?>
	<link href="style.css" rel="stylesheet" type="text/css">
	<link href="css/font-awesome.css" rel="stylesheet" type="text/css">
</head>


<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />


<meta name="viewport" content="target-densitydpi=device-dpi" />

<div class="light-status">
	<i id="lights-off" class="fa fa-lightbulb-o"></i>
	<i id="lights-on" class="fa fa-lightbulb-o"></i>
</div>

<?php
	if ($light_status == 'on') {
		?><script>$('#lights-off').hide();</script><?php
	} elseif ($light_status == 'off') {
		?><script>$('#lights-on').hide();</script><?php
	}
?>

<div id="clock"></div>

<div id="shade" class="thermostat-fade"></div>

<div id="thermostat"></div>

<div id="thermostat-settings" class="thermostat-fade">
	<div id="settings-header">Thermostat Settings</div>
	<div id="container"></div>
	<div class="settings"></div>
</div>

<!-- <form action="index.php" method="post">
	<input id="lights" type="submit" name="socket" value="toggle">
</form> -->

<!-- <i id="lights" class="fa fa-lightbulb-o"></i> -->

<div class="testblock"></div>

<div id="weather"></div>

<div id="old-media-id"></div>
<div id="media-id"><?php echo now_playing()['id']; ?></div>
<div id="matches"></div>

<input type="button" id="cache" value="" onClick="Kiosk.clearCaches(); Kiosk.reload(); return false;" />

<div id="now-playing">
</div>

	<style>
		input#lights:active, input#lights:hover {
			background: url(<?php echo $op_light_status; ?>.png)
		}
		input#lights {
			background: url(<?php echo $light_status; ?>.png);
		}
	</style>

	<script>
$(document).ready(function() {

	$('#thermostat-settings').hide();
	$('#shade').hide();
	$('#thermostat').load('roomtemp.php');
    var thermostatTimer =  function(){$('#thermostat').load('roomtemp.php');};
    setInterval(thermostatTimer,10000);


    var mediaIDTimer = function(){
    	// Set our current media ID
    	// Grab our new media ID
    	$('#media-id').load('mediaid.php #media-id');
    	$('#old-media-id').html($('#media-id').html());
    	// Check if our new ID is different than our older one
    	if ($("#media-id").text() != $("#old-media-id").text()) {
    		// If it is, reload nowplaying
    		$('#now-playing').load('nowplaying.php #now-playing');
    	}
    };
    setInterval(mediaIDTimer, 2000);

    $('#lights-on').click(function() {
    	$('#lights-on').load('lights.php #light-status', 'lights=off');
    	$('#lights-on').hide();
    	$('#lights-off').show();
    });

    $('#lights-off').click(function() {
    	$('#lights-off').load('lights.php #light-status', 'lights=on');
    	$('#lights-off').hide();
    	$('#lights-on').show();
    });

    $('#thermostat-settings .settings').load('thermostat-settings.php');

    function pad(num, size) {
    	var s = "000000000" + num;
    	return s.substr(s.length-size);
	}
    
    var clockTimer = function(){
    	date = new Date();
    	$('#clock').html(pad(date.getHours(),2)+":"+pad(date.getMinutes(),2));
    };

    setInterval(clockTimer, 2000);



    // var xbmcTimer =  function(){$('#now-playing').load('nowplaying.php', "id="+$("#media-id").html());};
    // setInterval(xbmcTimer,5000);
    
});
	</script>