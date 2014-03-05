<?php
// Runs the thermostat because we're awesome
$max_temp_difference = 1.5;
$thermostat_dir = '/etc/thermostat';
$heat_pin = 11;

// Decode our json files to arrays
$tempsFromFile = json_decode(file_get_contents($thermostat_dir . '/temps.json'), true);
$timesFromFile = json_decode(file_get_contents($thermostat_dir . '/times.json'), true);

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

$temperature = exec('/usr/bin/temp');
// Make sure we don't get a bum reading
while ($temperature < 40) {
	$temperature = exec('/usr/bin/temp');
}

// 70.6,2014,02,16,15,37
// Log our temperatures
$csvline[] = $temperature;
$csvline[] = date('o');
$csvline[] = date('m');
$csvline[] = date('d');
$csvline[] = date('H');
$csvline[] = date('i');

$csvline = implode(",", $csvline);

exec("echo " . $csvline . " >> " . $thermostat_dir . "/temp.log");
exec("tail -n 288 " . $thermostat_dir . "/temp.log > " . $thermostat_dir . "/temp.tmp");
exec("rm " . $thermostat_dir . "/temp.log");
exec("mv " . $thermostat_dir . "/temp.tmp " . $thermostat_dir . "/temp.log");


$set_temperature = $tempsFromFile[$last_time_occured];

if (($set_temperature - $temperature) >= $max_temp_difference) {
	// If the temp has dropped more than we want it to
	// Turn on the relay
	exec('sudo /usr/local/bin/gpio write ' .  $heat_pin . ' 1');
	// Update our relay status files
	exec('rm ' . $thermostat_dir . '/relay/0');
	exec('touch ' . $thermostat_dir . '/relay/1');
} elseif (($set_temperature - $temperature) <= 0) {
	// Otherwise our temp is okay
	exec('sudo /usr/local/bin/gpio write ' . $heat_pin . ' 0');
	// Update our relay status files
	exec('rm ' . $thermostat_dir . '/relay/1');
	exec('touch ' . $thermostat_dir . '/relay/0');
}

?>