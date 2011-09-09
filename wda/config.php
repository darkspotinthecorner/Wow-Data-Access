<?php
/* **************************************************************************
 * The WowDataAccess module handles the access (and caching) of World of
 * Warcraft related data through one or more data channels.
 * 
 * Copyright (C) 2011  Martin Gelder
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/gpl.html.
 * ************************************************************************** */

/**
 * config.php, Builds the base configuration for the channel manager
 * initialization. This is the file you need to edit if you want to implement
 * this module into your web application!
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// config.php

return(array(
	'config' => array(
		'logging'       => true,
		'loggingDirect' => false,
	),
	'defaults' => array(
		'locale' => WowDataAccess::LOCALE_EN_GB,
		'region' => WowDataAccess::REGION_EU,
		'realm'  => 'Gilneas',
	),
	'channels' => array(
		/*
		array(
			'class'   => 'WowDataChannelFilesystem',
		),
		// */
		/*
		array(
			'class'   => 'WowDataChannelMySQL',
			'options' => array(
				'mysql' => array(
					'server'   => 'localhost',
					'user'     => 'root',
					'password' => '',
					'database' => 'test',
				),
			),
		),
		// */
		array(
			'class' => 'WowDataChannelBattleNet',
			'options' => array(
				'api' => array(
					'auth' => false,
				),
			),
		),
	),
));

?>