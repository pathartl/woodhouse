<?php include 'xbmc.php'; ?>
<div id="now-playing">
	<table>
		<tr>
			<?php if (now_playing()['type'] == 'music') { ?>
				<td>
					<div class="song-title"><?php echo now_playing()['title']; ?></div>
					<div class="song-artist"><?php echo now_playing()['artist']; ?></div>
					<div class="song-album"><?php echo now_playing()['album']; ?></div>
					<?php exec('sudo wget -O thumbnail.jpg ' . now_playing()['thumbnail']); ?>
				</td>
				<td><img class="thumbnail" src="thumbnail.jpg?v=<?php echo time(); ?>"></td>
			<?php } elseif (now_playing()['type'] == 'movie') { ?>
				<td>
					<div class="movie-title"><?php echo now_playing()['title']; ?></div>
					<div class="movie-plot"><?php echo now_playing()['plot']; ?></div>
					<?php 
							exec('sudo wget -O thumbnail.jpg ' . now_playing()['thumbnail']);
					?>
				</td>
				<td id="thumbnail"><img class="thumbnail poster" src="thumbnail.jpg?v=<?php echo time(); ?>"></td>
			<?php } elseif (now_playing()['type'] == 'episode') { ?>

				<td>
					<div class="episode-show"><?php echo now_playing()['showtitle']; ?></div>
					<div class="episode-title">
						<?php
							echo 'S' . str_pad(now_playing()['season'], 2, '0', STR_PAD_LEFT) .
								'E' . str_pad(now_playing()['episode'], 2, '0', STR_PAD_LEFT) .
								': ' . now_playing()['title'];
						?>
					</div>
					<div class="movie-plot"><?php echo now_playing()['plot']; ?></div>
					<?php exec('sudo wget -O thumbnail.jpg ' . now_playing()['poster']); ?>
					<td id="thumbnail"><img class="thumbnail poster" src="thumbnail.jpg?v=<?php echo time(); ?>"></td>
				</td>

			<?php } ?>
		</tr>
	</table>
</div>