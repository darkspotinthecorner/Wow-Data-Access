<?php
/**
 * example.php, rudimentary example of a wda implementation
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 */
// example.php

$wda    = require_once(dirname(__FILE__).'/wda/start.php');
$params = array(
	'icon'      => array('icon'  => 'inv_misc_stonedragonblue'),
	'realm'     => array('realm' => 'Confrérie du Thorium'),
	'character' => array('realm' => 'Gilneas', 'character' => 'Rhil'),
	'guild'     => array('realm' => 'Gilneas', 'guild' => 'Ascension'),
);

$icon      = $wda->openIcon($params['icon']);
$realm     = $wda->openRealm($params['realm']);
$character = $wda->openCharacter($params['character']);
$guild     = $wda->openGuild($params['guild']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>WowDataAccess Example Usage</title>
	<style type="text/css">
		body {
			font-family: monospace;
			font-size: 14px;
		}
	</style>
</head>
<body>
	<h1>Wow Data Access - Demo Page</h1>
	<p><a href="http://code.google.com/p/wow-data-access/">Project Homepage</a></p>
	<p><em>All queries are run on the european region with an english locale.</em></p>
	<p><em>This demo uses the default config file: <strong>wda/config.php</strong></em></p>
	
	<hr />
	
	<p>
		<div>Loading the icon "<?php echo($params['icon']['icon']); ?>":</div>
		<div><pre><img src="data:image/jpeg;base64,<?php echo(base64_encode($icon->get('image'))); ?>" alt="<?php echo($icon->get('icon')); ?>"/></pre></div>
	</p>
	
	<hr />
	
	<p>
		<div>Loading the realm "<?php echo($params['realm']['realm']); ?>":</div>
		<div><pre><?php print_r(($realm && (get_class($realm) == 'WowDataRealm') && $realm->isValid() ? $realm->get() : $realm)); ?></pre></div>
	</p>
	
	<hr />
	
	<p>
		<div>Loading the character "<?php echo($params['character']['character']); ?> @ <?php echo($params['character']['realm']); ?>":</div>
		<div>
			<pre><?php
					if($character && (get_class($character) == 'WowDataCharacter') && $character->isValid()) {
						print_r($character->get());
						$thumbnail = $wda->openCharacterThumbnail(array_merge(array('filename' => $character->get('thumbnail')), $params['character']));
						if($thumbnail && (get_class($thumbnail) == 'WowDataCharacterThumbnail') && $thumbnail->isValid()) {
							echo('<br /><img src="data:image/jpeg;base64,'.base64_encode($thumbnail->get('image')).'" alt="'.$thumbnail->get('filename').'"/>');
						}
					}
			?></pre>
		</div>
	</p>
	
	<hr />
	
	<p>
		<div>Loading the guild "&lt;<?php echo($params['guild']['guild']); ?>&gt; @ <?php echo($params['guild']['realm']); ?>":</div>
		<div><pre><?php print_r(($guild && (get_class($guild) == 'WowDataGuild') && $guild->isValid() ? $guild->get() : $guild)); ?></pre></div>
	</p>
	
	<hr />
	
	<h2>Logs:</h2>
	<p>
		<div>
			<?php
				$index = 0;
				foreach($wda->log() as $entry) {
					echo('<pre style="padding:0.5em; margin:1em; border:2px solid #999; background-color:#ddd;"><div style="background-color:#bbb; padding: 0.25em;">Log entry #'.($index + 1).' ('.date('H:i:s:u - d.m.Y', $entry['time']).')</div>'.$entry['message'].'</pre>');
				}
			?>
		</div>
	</p>
</body>
</html>