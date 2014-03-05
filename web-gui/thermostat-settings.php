<?php
	 $set_temp = exec('ls /etc/thermostat/temperature');
	// if ($_GET['temp']) {
	// 	if ($_GET['temp'] != $set_temp) {
	// 		exec('rm /etc/thermostat/temperature/' . $set_temp);
	// 		exec('touch /etc/thermostat/temperature/' . $_GET['temp']);
	// 	}
	// }

	// If we have at least one temperature value set
	// Let's find how many there are and dump them into an array
	$tempsFromSettings = array();
	$timesFromSettings = array();
	$daysFromSettings = array();
	for ($k = 0; $k >= 0; $k++) {
		// If we find a temp
		if ($_GET['temp' . $k]) {
			// Add it to our array
			$tempsFromSettings[] = $_GET['temp' . $k];
			$timesFromSettings[] = $_GET['time' . $k];
			$daysFromSettings[] = array('sun' => $_GET['sun' . $k],
										'mon' => $_GET['mon' . $k],
										'tue' => $_GET['tue' . $k],
										'wed' => $_GET['wed' . $k],
										'thu' => $_GET['thu' . $k],
										'fri' => $_GET['fri' . $k],
										'sat' => $_GET['sat' . $k]);
		} else {
			// Else if we're at the end of our numbers
			$k = -2;
		}
	}
	if ($_GET['removerow']) {
		unset($tempsFromSettings[$_GET['removerow']]);
		unset($timesFromSettings[$_GET['removerow']]);
		unset($daysFromSettings[$_GET['removerow']]);
	}
	if ($_GET) {
		file_put_contents('/etc/thermostat/temps.json', json_encode($tempsFromSettings));
		file_put_contents('/etc/thermostat/times.json', json_encode($timesFromSettings));
		file_put_contents('/etc/thermostat/days.json', json_encode($daysFromSettings));
	}

?>
<form action="">

<?php

// Decode our json files to arrays
$tempsFromFile = json_decode(file_get_contents('/etc/thermostat/temps.json'), true);
$timesFromFile = json_decode(file_get_contents('/etc/thermostat/times.json'), true);
$daysFromFile = json_decode(file_get_contents('/etc/thermostat/days.json'), true);

$numOfSchedules = count($tempsFromFile);

if ($_GET['addrow'] == 1) {
	$numOfSchedules++;
}

for ($i = 0; $i < $numOfSchedules; $i++) { ?>
	<div class="schedule-row">
		<div class="remove-row" onclick="saveSchedules('&removerow=<?php echo $i; ?>');">-</div>
		At
		<div class="time-field">
			<input type="time" name="temp-time<?php echo $i; ?>" id="temp-time<?php echo $i; ?>" value="<?php echo $timesFromFile[$i]; ?>">
			<div class="field-bracket"></div>
		</div>
		set to
		<label class="select">
		<select id="set-temp<?php echo $i; ?>" class="set-temp" name="set-temp<?php echo $i; ?>">
			<?php
				for ($j = 60; $j <= 75; $j++) {
					if ($j == $tempsFromFile[$i]) {
						echo '<option value="' . $j . '" selected>' . $j . '</option>';
					} else {
						echo '<option value="' . $j . '">' . $j . '</option>';
					}
				} 
			?>
		</select>
		</label>
		on
		<table id="set-day<?php echo $i; ?>" class="set-day">
		  <tr>
		  	<td><input type="checkbox" id="sun<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['sun'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="mon<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['mon'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="tue<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['tue'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="wed<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['wed'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="thu<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['thu'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="fri<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['fri'] == 'true') echo 'checked'; ?>>
		  	<td><input type="checkbox" id="sat<?php echo $i; ?>" value="1" <?php if ($daysFromFile[$i]['sat'] == 'true') echo 'checked'; ?>>
		  </tr>
		  <tr>
		  	<td>S
		  	<td>M
		  	<td>T
		  	<td>W
		  	<td>T
		  	<td>F
		  	<td>S
		  </tr>
		</table>
	</div>
<?php } ?>
	<div class="schedule-row">
		<button type="button" id="add-row">Add Row</button>
	</div>

<script type="text/javascript">

	function saveSchedules(extra) {
		var tempString = '?save=true';
		var i = 0;
		for (i = 0; i < 10; i++) {
			// If our next row exists
			 if ($('#set-temp'+i).length != 0) {
				// Add its data to our GET data set
				tempString += '&temp' + i + '=' + $('#set-temp'+i).val();
				tempString += '&time' + i + '=' + $('#temp-time'+i).val();
				tempString += '&sun' + i + '=' + $('#sun'+i).prop('checked');
				tempString += '&mon' + i + '=' + $('#mon'+i).prop('checked');
				tempString += '&tue' + i + '=' + $('#tue'+i).prop('checked');
				tempString += '&wed' + i + '=' + $('#wed'+i).prop('checked');
				tempString += '&thu' + i + '=' + $('#thu'+i).prop('checked');
				tempString += '&fri' + i + '=' + $('#fri'+i).prop('checked');
				tempString += '&sat' + i + '=' + $('#sat'+i).prop('checked');
			 } else {
			 	i = 11;
			 }
		}
		if (extra) {
			tempString += extra;
		}
    	$('#thermostat-settings .settings').load('thermostat-settings.php', tempString);	
	}

	$('button#temp-submit').click(function() {
		saveSchedules();
		$('.thermostat-fade').fadeOut(200);
    });

	$('button#add-row').click(function() {
		saveSchedules('&addrow=1');
    });

</script>
<button type="button" id="temp-submit" >Set Temp</button><!-- 
<button type="button" onclick="$('#thermostat-settings').hide();$('#shade').fadeOut(200);$('#thermostat-settings .settings').load('thermostat-settings.php', 'temp='+$('#set-temp option:selected').text());">Set Temp</button> -->
</form>