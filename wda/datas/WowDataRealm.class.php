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
 * WowDataRealm.class.php, Realm Data definition
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataRealm.class.php

/**
 * Defined data class that represents a wow realm
 * @package wow-data-access
 * @subpackage wow-data
 */
class WowDataRealm extends WowData {
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array_merge(parent::getMetaData(), array(
			// --- region ---------------------------------------- R -
			'region' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Realm region key must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Realm region key must not contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'region',
			),
			// --- slug ------------------------------------------ R -
			'slug' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Realm slug must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Realm slug must be a valid key.'),
				),
				'clean'   => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'realm',
			),
			// --- name ------------------------------------------ R -
			'name' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Realm name must not be empty.'),
				),
			),
			// --- type ------------------------------------------ R -
			'type' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Realm type must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Realm type must be a valid key.'),
				),
				'clean'   => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- population ----------------------------------------
			'population' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Realm population must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Realm population must be a valid key.'),
				),
				'clean'   => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- queue ---------------------------------------------
			'queue' => array(
				'required' => true,
				'clean'   => array(
					array('method' => 'cleanBoolean', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- status --------------------------------------------
			'status' => array(
				'required' => true,
				'clean'   => array(
					array('method' => 'cleanBoolean', 'params' => array(self::INPUT_TOKEN)),
				),
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