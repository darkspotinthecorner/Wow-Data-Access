<?php
/**
 * authed.php, rudimentary example of a wda implementation with authentication
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 */
// auth.php

require_once(dirname(__FILE__).'/wda/load.php');

$wda = WowDataAccess::obj(array(
	'config' => array(
		'logging'       => true,
		'loggingDirect' => false,
	),
	'defaults' => array(
		'locale' => WowDataAccess::LOCALE_EN_US,
		'region' => WowDataAccess::REGION_US,
		'realm'  => 'Gilneas',
	),
	'channels' => array(
		array(
			'class'   => 'WowDataChannelBattleNet',
			'options' => array(
				'api' => array(
					'auth' => array(
						'publickey'  => (isset($_GET['publickey'])  ? $_GET['publickey'] : 'dummy-public'),
						'privatekey' => (isset($_GET['privatekey']) ? $_GET['privatekey']: 'dummy-private'),
					),
				),
			),
		),
	),	
));

$item = $wda->openItem(array('itemid' => (isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : 49623)));

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
	<h1>Wow Data Access - Authentication Demo Page</h1>
	<p><a href="http://code.google.com/p/wow-data-access/">Project Homepage</a></p>
	<p><em>All queries are run on the european region with an english locale.</em></p>
	
	<hr />
	
	<p>
		<form method="get">
			<label>API Public Key: <input type="text" name="publickey" id="form_text_privatekey" value="<?php echo(isset($_REQUEST['publickey']) ? $_REQUEST['publickey'] : ''); ?>"/></label>
			<label>API Private Key: <input type="text" name="privatekey" id="form_text_publickey" value="<?php echo(isset($_REQUEST['privatekey']) ? $_REQUEST['privatekey'] : ''); ?>"/></label>
			<label>Item ID: <input type="text" name="itemid" id="form_text_itemid" value="<?php echo(isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : ''); ?>"/></label>
			<input type="submit" value="Lookup!" />
		</form>
	</p>
	
	<p>
		<div>Loading the item id 49623:</div>
		<div><pre><?php print_r(($item && (get_class($item) == 'WowDataItem') && $item->isValid() ? $item->get() : $item)); ?></pre></div>
	</p>
	
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