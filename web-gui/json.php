<?php

	define('XBMC_USER', 'xbmc');
	define('XBMC_PASS', 'xbmc');
	define('XBMC_SERVER', '192.168.1.10');
	define('XBMC_PORT', 8085);

	// echo "<pre>";
	// print_r(get_xbmc_info('{"jsonrpc": "2.0", "method": "Player.GetItem", "params": { "properties": ["title", "album", "artist", "thumbnail", "fanart"], "playerid": 0 }, "id": "AudioGetItem"}'));
	// echo "</pre>";

	// echo now_playing()['artist'];
	// echo now_playing()['album'];
	// echo now_playing()['title'];
	// // echo '<img src="' . 'http://xbmc:xbmc@192.168.1.10:8085/image/' . urlencode(now_playing()['thumbnail']) . '">';
	// echo '<img src="' . now_playing()['thumbnail'] . '">';

	json_test();

	function get_xbmc_info($request) {

		$url = 'http://' . XBMC_USER . ':' . XBMC_PASS . '@' . XBMC_SERVER .
		  ':' . XBMC_PORT . '/jsonrpc?request=' . urlencode($request);

		$json = file_get_contents($url);
		$json_result = json_decode($json, TRUE);
		return $json_result;
	}

	function json_test() {

		// See what kind of media the player is currently playing
		$artists = get_xbmc_info('{"jsonrpc": "2.0", "method": "AudioLibrary.GetArtists", "params": { "sort": { "order": "ascending", "method": "artist", "ignorearticle": true } }, "id": 1}');

		// $albums = $albums['result']['albums'];
		$artists = $artists['result']['artists'];

		$musiclibrary = array();

		echo '<div id="music-library">';

		foreach ($artists as $artist) {
			// Get the albums for each artist
			?>
			<div class="artist"> 
				<div class="artist-title" id="<?php echo $artist['artistid']; ?>"><?php echo $artist['label']; ?></div>
			<?php
			$artist['albums'] = get_xbmc_info('{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",
				"params": { "filter": { "field": "artist", "operator": "is", "value": "' . $artist['artist'] . '" }, "properties": ["thumbnail"], "sort": { "order": "ascending", "method": "album", "ignorearticle": true } },
				"id": "libAlbums"}');

			// Remove unnecessary info
			$artist['albums'] = $artist['albums']['result']['albums'];

			$i = 0;
			foreach ($artist['albums'] as $album) {
				// Get the songs per album
				$artist['albums'][$i]['songs'] = get_xbmc_info('{"jsonrpc": "2.0", "method": "AudioLibrary.GetSongs",
				"params": { "filter": { "field": "album", "operator": "is", "value": "' . $album['label'] . '" }, "properties": ["duration"], "sort": { "order": "ascending", "method": "track", "ignorearticle": true } },
				"id": "libSongs"}');
				$artist['albums'][$i]['songs'] = $artist['albums'][$i]['songs']['result']['songs'];
				$i++;
			}

			?></div><?php

			// Add to main music library
			$musiclibrary[] = $artist;
		}

		echo "</div>";

		// echo "<pre>";
		// print_r($musiclibrary);
		// echo "</pre>";
}
?>