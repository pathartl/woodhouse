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
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights off');
		break;
	// Turn all the lights on
	case 'allon':
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights on');
		break;
	// Toggle the lights based on the status of the majority
	case 'toggle':
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights all');
		break;
	default:
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights toggle' . $_POST['socket']);
}

?><div class="light-status"><?php
if (file_exists("/etc/lights/0")) {
	$light_status = "off";
	$op_light_status = "on";
	if ($_GET['lights'] == 'on') {
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights on');
	}
} else {
	$light_status = "on";
	$op_light_status = "off";
	if ($_GET['lights'] == 'off') {
		exec('sudo /home/pathartl/Development/thermo-pi/lights/lights off');
	}
}

?></div>