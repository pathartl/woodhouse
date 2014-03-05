<?php

// Runs the thermostat because we're awesome

// Decode our json files to arrays
$tempsFromFile = json_decode(file_get_contents('/etc/thermostat/temps.json'), true);
$timesFromFile = json_decode(file_get_contents('/etc/thermostat/times.json'), true);

$epoch_times = array();

foreach ($timesFromFile as $time) {
	$epoch_times[] = strtotime($time);
}

array_multisort($epoch_times, $tempsFromFile, $timesFromFile);

$last_time_occured = 0;
// For each schedule entry
for ($i = 0; $i < count($tempsFromFile); $i++) {
	// Check to see if the time has already happened and exists
	if (strtotime($timesFromFile[$i]) < time() && $timesFromFile[$i] != '') {
		$last_time_occured = $i;
	}
}

//Grab our current set temperature
$set_temp = exec('ls /etc/thermostat/temperature');

if ($tempsFromFile[$last_time_occured] != $set_temp) {

	exec('rm /etc/thermostat/temperature/' . $set_temp);

	// Create our new temperature
	exec('touch /etc/thermostat/temperature/' . $tempsFromFile[$last_time_occured]);

}

?>