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
 * WowDataGuild.class.php, Guild Data definition
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataGuild.class.php

/**
 * Defined data class that represents a wow guild
 * @package wow-data-access
 * @subpackage wow-data
 */
class WowDataGuild extends WowDataBase {
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array_merge(parent::getMetaData(), array(
			// --- realm ----------------------------------------- R -
			'realm' => array(
				'required' => true,
				'param'    => 'realm',
			),
			// --- name ------------------------------------------ R -
			'name' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength',     'params' => array(self::INPUT_TOKEN, 2, 24),  'feedback' => 'Guild name must be between 2 and 24 characters length.'),
					array('method' => 'verifyGuildName',        'params' => array(self::INPUT_TOKEN),         'feedback' => 'Guild name must not contain special characters.'),
					array('method' => 'verifyMaxRepeatedChars', 'params' => array(self::INPUT_TOKEN, 1, ' '), 'feedback' => 'Guild name must not contain more than one consecutive whitespace.'),
					array('method' => 'verifyMaxRepeatedChars', 'params' => array(self::INPUT_TOKEN, 2),      'feedback' => 'Guild name must not contain more than two identical consecutive characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanGuildName', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'guild',
			),
		)));
	}
	
	/**
	 * Returns the i18n data added by this class
	 * @return array associative array containing the i18n data
	 */
	static public function getI18NData() {
		return(array_merge(parent::getI18NData(), array()));
	}
}

?>