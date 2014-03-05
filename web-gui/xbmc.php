<?php
	include 'config.php';
	now_playing();

	function get_xbmc_info($request) {

		$url = 'http://' . XBMC_USER . ':' . XBMC_PASS . '@' . XBMC_SERVER .
		  ':' . XBMC_PORT . '/jsonrpc?request=' . urlencode($request);

		$json = file_get_contents($url);
		$json_result = json_decode($json, TRUE);
		return $json_result;
	}

	function now_playing() {

		// See what kind of media the player is currently playing
		$json_result = get_xbmc_info('{"jsonrpc": "2.0", "method": "Player.GetActivePlayers", "id": 1}');

		// If we're playing audio
		if ($json_result['result'][0]['type'] == "audio") {
			// Return audio data
			$audiodata = get_xbmc_info('{"jsonrpc": "2.0", "method": "Player.GetItem", "params": { "properties": ["title", "album", "artist", "thumbnail"], "playerid": 0 }, "id": "AudioGetItem"}');
			$audiodata = $audiodata['result']['item'];
			$audiodata['artist'] = $audiodata['artist'][0];
			$audiodata['thumbnail'] = 'http://' . XBMC_USER . ':' . XBMC_PASS . '@' . XBMC_SERVER .
										':' . XBMC_PORT . '/image/' . urlencode($audiodata['thumbnail']);
			$audiodata['type'] = 'music';


			return $audiodata;
		} elseif ($json_result['result'][0]['type'] == "video") {
			$videodata = get_xbmc_info('{"jsonrpc": "2.0", "method": "Player.GetItem", "params": { "properties": ["title", "album", "artist", "season", "episode", "duration", "showtitle", "thumbnail", "plot", "art"], "playerid": 1 }, "id": "VideoGetItem"}');
			
			$videodata = $videodata['result']['item'];
			$videodata['thumbnail'] = 'http://' . XBMC_USER . ':' . XBMC_PASS . '@' . XBMC_SERVER .
										':' . XBMC_PORT . '/image/' . urlencode($videodata['thumbnail']);
			$videodata['poster'] = 'http://' . XBMC_USER . ':' . XBMC_PASS . '@' . XBMC_SERVER .
										':' . XBMC_PORT . '/image/' . urlencode($videodata['art']['tvshow.poster']);

			return $videodata;
		}
	}

?>