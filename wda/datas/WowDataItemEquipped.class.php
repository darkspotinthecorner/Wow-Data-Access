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
 * WowDataItemEquipped.class.php, Equipped Item Data definition
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataItemEquipped.class.php

/**
 * Defined data class that represents an equipped wow item
 * @package wow-data-access
 * @subpackage wow-data
 */
class WowDataItemEquipped extends WowDataItem {
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
			// --- character ------------------------------------- R -
			'character' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyCharacterName', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item character name must contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanCharacterName', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'character',
			),
			// --- slotId ---------------------------------------- R -
			'slotId' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item slot id must be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'slotid',
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