<a id="cur-temp">
    <?php 
        $temperature = exec('/usr/bin/temp');
        // Make sure we don't get a bum reading
        while ($temperature < 40) {
            $temperature = exec('/usr/bin/temp');
        }
    echo $temperature; ?>&deg;F
</a>
<?php
if (file_exists("/etc/thermostat/relay/0")) {
	// Relay is off
	?> <div id="furnace-status">Heat Off</div> <?php
} else {
	// Relay is on
	?> <div id="furnace-status">Heat On</div> <?php
}
?>
<script>
    $('#cur-temp').click(function() {
    	$('#thermostat-settings .settings').load('thermostat-settings.php');
    	$('.thermostat-fade').fadeIn(200);
    	// $('#thermostat-settings').show();
    	// $('#shade').fadeIn(200);
    });
    $('#shade').click(function() {
    	$('.thermostat-fade').fadeOut(200);
    	// $('#thermostat-settings').hide();
    	// $('#shade').fadeOut(200);
    });
</script>