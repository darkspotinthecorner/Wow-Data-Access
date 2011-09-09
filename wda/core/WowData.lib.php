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
 * WowData.lib.php, Data definitions
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowData.lib.php

/**
 * Basic data class that provides all the methods
 * @package wow-data-access
 * @subpackage wow-data
 */
abstract class WowData {
	/**
	 * Replacement token for input value
	 * @var string
	 */
	const INPUT_TOKEN = '[?--[WowDataInput]--?]';
	
	/**
	 * Characters allowed in keys
	 * @var string
	 */
	const ALLOWED_CHARS_KEY = 'a-zA-Z0-9_\\-';
	
	/**
	 * Characters allowed in file names
	 * @var string
	 */
	const ALLOWED_CHARS_FILENAME = 'a-zA-Z0-9_\\-\\. ';
	
	/**
	 * Characters allowed in file names
	 * @var string
	 */
	const ALLOWED_CHARS_FILEPATH = '\/a-zA-Z0-9_\\-\\. ';
	
	/**
	 * Characters allowed in character names
	 * @var string
	 */
	const ALLOWED_CHARS_CHARACTERNAME = 'a-zA-ZŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿАБВГҐДЂЃЕЀЁЄЖЗЅИЍІЇЙЈКЛЉМНЊОПРСТЋЌУЎФХЦЧЏШЩЪЫЬЭЮЯ';
	
	/**
	 * Characters allowed in guild names
	 * @var string
	 */
	const ALLOWED_CHARS_GUILDNAME = 'a-zA-ZŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿАБВГҐДЂЃЕЀЁЄЖЗЅИЍІЇЙЈКЛЉМНЊОПРСТЋЌУЎФХЦЧЏШЩЪЫЬЭЮЯ ';
	
	/**
	 * Stored data
	 * @var mixed
	 */
	protected $data = array();
	
	/**
	 * Merged meta data
	 * @var mixed
	 */
	protected $datameta = array();
	
	/**
	 * i18n data
	 * @var mixed
	 */
	protected $datai18n = array();
	
	/**
	 * Time relevance
	 * @var integer
	 */
	protected $timestamp = null;
	
	/**
	 * Contructor that will merge the meta data and parse any input parameters
	 * @param array $input associative array of data ids and values
	 */
	public function __construct(array $input = array()) {
		/**
		 * Requires PHP 5.3.0
		 * Dirty hack since self::getMetaData() will always call WowData::getMetaData(), even in subclasses ;-)
		 */
		$classname = get_class($this);
		$this->datameta = $classname::getMetaData();
		$this->datai18n = $classname::getI18NData();
		
		/**
		 * Parse input into data storage
		 */
		foreach($input as $key => $value) {
			$this->set($key, $value);
		}
		$this->timestamp = time();
	}
	
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array());
	}
	
	/**
	 * Returns the i18n data added by this class
	 * @return array associative array containing the i18n data
	 */
	static public function getI18NData() {
		return(array());
	}
	
	/**
	 * Returns an array that maps all params available for data ids
	 * @return array the map that has the parameters as keys and the data ids as values
	 */
	public function getParamDataIdMap() {
		$c = array();
		foreach($this->datameta as $dataid => $metainfo) {
			if(isset($metainfo['param'])) {
				$c[$metainfo['param']] = $dataid;
			}
		}
		return($c);
	}
	
	/**
	 * Compares a parameter array with the object
	 * @param array $params the parameter array
	 * @return boolean true if the params match the object, false if not
	 */
	public function matchParams($params) {
		$pMap  = $this->getParamDataIdMap();
		$match = 0;
		foreach($params as $pkey => $pvalue) {
			if(isset($pMap[$pkey]) && ($this->get($pMap[$pkey]) == $pvalue)) {
				$match++;
			}
		}
		return($match == count($pMap));
	}
	
	/**
	 * Sets or retrieves the timestamp
	 * @param integer|null $set the timestamp to insert, null to retrieve the current timestamp
	 * @return boolean|integer success report of insertion, or the current timestamp
	 */
	public function timestamp($set = null) {
		if($set === null) {
			return($this->timestamp);
		} else {
			return($this->timestamp = intval($set));
		}
	}
	
	/**
	 * Adds data to a specified data id
	 * @param string $key data id to which the value should be appended
	 * @param mixed $value content to append
	 * @return WowData the object itself to support chaining
	 */
	public function add($key, $value) {
		$this->_add($key, $value);
		return($this);
	}
	
	/**
	 * Sets data for a specified data id
	 * @param string $key data id for which the value will be set
	 * @param mixed $value content to set
	 * @return WowData the object itself to support chaining
	 */
	public function set($key, $value) {
		$this->_set($key, $value);
		return($this);
	}
	
	/**
	 * Fetches the data of one or more data ids
	 * @param string|array $key data id or array of "data id" => "return key" pairs
	 * @return mixed the content of the data id or an array of "return key" => "value of the data id" pairs
	 */
	public function get($key = null) {
		if($key === null) {
			return($this->data);
		}
		if(is_string($key)) {
			return($this->_get($key));
		}
		if(is_array($key) && !empty($key)) {
			$result = array();
			foreach($key as $subkey => $subvalue) {
				$result[$subkey] = $this->_get($subvalue);
			}
			return($result);
		}
		return(null);
	}
	
	/**
	 * Internal method that handles the addition of data
	 * @param string $key data id to which the value should be appended
	 * @param mixed $value content to append
	 * @param mixed $meta for nested data this will contain a meta data subtree that applies to the nested data
	 * @return boolean true on successful data adding, false on failure
	 */
	protected function _add($key, $value, $meta = null) {
		/**
		 * If there is no meta subtree given, we use the whole meta data
		 */
		if($meta === null) {
			$meta = $this->datameta;
		}
		
		/**
		 * To add data to a data id, it must be defined as a collection
		 */
		if(isset($meta[$key]['collect']) && ($meta[$key]['collect'] == true)) {
			if($this->isValidInput($key, $value, $meta)) {
				
				/**
				 * If there is already data for this data id, we check if the data is
				 * inserted in a collector-friendly manner. If it is not, we make it
				 * collector-friendly by pushing it into an indexed array as the first
				 * element
				 */
				if(!empty($this->data[$key]) && !$this->isCollector($this->data[$key])) {
					$this->data[$key] = array($this->data[$key]);
				}
				
				/**
				 * If the passed data is already a collection, iterate over it and add each
				 * element individually, if not we set the data directly
				 */
				if($this->isCollector($value)) {
					$status = true;
					foreach($value as $subvalue) {
						$status = ($this->data[$key][] = $this->cleanInput($key, $subvalue, $meta)) && $status;
					}
					return($status);
				} else {
					return($this->data[$key][] = $this->cleanInput($key, $value, $meta));
				}
			}
		} else {
			$this->publishFeedback('Adding to "'.$key.'" is not possible, it\'s not definded as a collection.');
		}
		return(false);
	}
	
	/**
	 * Internal method that handles the setting of data
	 * @param string $key data id for which the value will be set
	 * @param mixed $value content to set
	 * @param mixed $meta for nested data this will contain a meta data subtree that applies to the nested data
	 * @return boolean true on successful data adding, false on failure
	 */
	protected function _set($key, $value, $meta = null) {
		/**
		 * If there is no meta subtree given, we use the whole meta data
		 */
		if($meta === null) {
			$meta = $this->datameta;
		}
		
		/**
		 * If we have valid data, set it into the data id
		 */
		if($this->isValidInput($key, $value, $meta)) {
			if(isset($meta[$key]['collect']) && ($meta[$key]['collect'] == true)) {
				if($this->isCollector($value)) {
					return($this->data[$key] = $this->cleanInput($key, $value, $meta));
				} else {
					return($this->data[$key] = array($this->cleanInput($key, $value, $meta)));
				}
			} else {
				return($this->data[$key] = $this->cleanInput($key, $value, $meta));
			}
		}
		return(false);
	}
	
	/**
	 * Internal method that handles the retrieval of data
	 * @param string $key data id to fetch
	 * @return mixed the data for this data id
	 */
	protected function _get($key) {
		if($this->isValidDataID($key)) {
			if(isset($this->data[$key])) {
				return($this->data[$key]);
			}
		} else {
			die('<pre>'.print_r($this->datameta, true).'</pre>');
			WowDataAccess::obj()->error('Data Access Error in "'.__CLASS__.'" - Data ID "'.$key.'" is not defined!', 500);
		}
		return(null);
	}
	
	/**
	 * Method to publish feedback
	 * @param string $feedback the feedback message
	 */
	protected function publishFeedback($feedback) {
		trigger_error($feedback, E_USER_NOTICE);
	}
	
	/**
	 * Checks if all of the object's required data is present
	 * @return boolean true if all required data is set, false if not
	 */
	public function isValid() {
		foreach($this->datameta as $key => $meta) {
			if(($meta['required'] === true) && (!isset($this->data[$key]))) {
				return(false);
			}
		}
		return(true);
	}
	
	/**
	 * Checks if all required data ids are present as values in the input array,
	 * any missing data ids will be returned as an array
	 * @param array $input indexed array with data ids as values
	 * @return array any missing required data ids
	 */
	public function compareForMissingRequiredData(array $input) {
		$c = array();
		foreach($this->datameta as $key => $meta) {
			if(($meta['required'] === true) && (!in_array($key, $input))) {
				$c[] = $key;
			}
		}
		return($c);
	}
	
	/**
	 * Checks if all data ids in the input array are valid data ids, any data ids
	 * that are not defined will be returned as an array
	 * @param array $input indexed array with data ids as values
	 * @return array any undefined data ids
	 */
	public function compareForMissingDefinitions(array $input) {
		$c = array();
		foreach($input as $key) {
			if(!$this->isValidDataID($key)) {
				$c[] = $key;
			}
		}
		return($c);
	}
	
	/**
	 * Checks if an array has only numeric keys
	 * @param array $input array to check
	 * @return boolean true if only numeric keys exist, false if not
	 */
	protected function isCollector($input) {
		if(!empty($input) && is_array($input)) {
			foreach($input as $key => $value) {
				if(!is_int($key)) {
					return(false);
				}
			}
			return(true);
		}
		return(false);
	}
	
	/**
	 * Checks a data id is defined
	 * @param string $key data id to check for
	 * @return boolean true if the data id is defined, false if not
	 */
	protected function isValidDataID($key) {
		return(isset($this->datameta[$key]));
	}
	
	/**
	 * Checks if data input is valid by comparing it to the data id's meta data
	 * @param string $key data id to check
	 * @param mixed $value value to verify
	 * @param mixed $meta for meta data subtree, if we verify a root data id, this will be the whole meta data
	 * @return boolean true if the value is valid for this data id, false if not
	 */
	protected function isValidInput($key, $value, $meta) {
		if(isset($meta[$key]) && (!isset($meta[$key]['input']) || ($meta[$key]['input'] === true))) {
			if($this->isCollector($value)) {
				if(isset($meta[$key]['collect']) && ($meta[$key]['collect'] == true)) {
					$status = true;
					foreach($value as $subvalue) {
						$status = $this->isValidInputRec($key, $subvalue, $meta) && $status;
					}
					return($status);
				} else {
					$this->publishFeedback('Value of "'.$key.'" must not be a collection.');
					return(false);
				}
			} else {
				return($this->isValidInputRec($key, $value, $meta));
			}
		}
	}
	
	/**
	 * Checks recursively if data input is valid by comparing it to the data id's meta data
	 * @param string $key data id to check
	 * @param mixed $value value to verify
	 * @param mixed $meta for meta data subtree, if we verify a root data id, this will be the whole meta data
	 * @return boolean true if the value is valid for this data id, false if not
	 */
	protected function isValidInputRec($key, $value, $meta) {
		if(isset($meta[$key]) && (!isset($meta[$key]['input']) || ($meta[$key]['input'] === true))) {
			if(isset($meta[$key]['subset']) && is_array($meta[$key]['subset']) && (!empty($meta[$key]['subset']))) {
				if(!is_array($value)) {
					$this->publishFeedback('Value of "'.$key.'" must be an array.');
					return(false);
				}
				$status = true;
				foreach($value as $subkey => $subvalue) {
					$status = $this->isValidInputRec($subkey, $subvalue, $meta[$key]['subset']) && $status;
				}
				return($status);
			}
			return($this->verifyInput($key, $value, $meta));
		}
		return(false);
	}
	
	/**
	 * The atmoic checking method that executes all methods defined in the meta
	 * data to determine if the value is valid for the data id
	 * @param string $key data id to check
	 * @param mixed $value value to verify
	 * @param mixed $meta for meta data subtree, if we verify a root data id, this will be the whole meta data
	 * @return boolean true if the value is valid for this data id, false if not
	 */
	protected function verifyInput($key, $value, $meta) {
		$status = true;
		if(isset($meta[$key]['verify']) && !empty($meta[$key]['verify'])) {
			$verify = $meta[$key]['verify'];
			foreach($verify as $step) {
				foreach($step['params'] as &$param) {
					if($param === self::INPUT_TOKEN) {
						$param = $value;
					}
				}
				unset($param);
				$r = call_user_func_array(array($this, $step['method']), $step['params']);
				if($r === false) {
					$this->publishFeedback($step['feedback']);
					$status = false;
				}
			}
		}
		return($status);
	}
	
	/**
	 * Verifies an integer input
	 * @param mixed $input value to verify
	 * @return boolean true if the value is an integer, false if not
	 */
	protected function verifyInteger($input) {
		if(strval(intval($input)) === strval($input)) {
			return(true);
		}
		return(false);
	}
	
	/**
	 * Verifies a positive integer input (not negative, not zero)
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a positive integer, false if not
	 */
	protected function verifyPositiveInteger($input) {
		if(intval($input) <= 0) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a non-negative integer input (positive or zero)
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a non-negative integer, false if not
	 */
	protected function verifyNonNegativeInteger($input) {
		if(intval($input) >= 0) {
			return(true);
		}
		return(false);
	}
	
	/**
	 * Verifies an id (alias to $this->verifyPositiveInteger)
	 * @param mixed $input value to verify
	 * @return boolean true if the value is an id, false if not
	 */
	protected function verifyId($input) {
		return($this->verifyPositiveInteger($input));
	}
	
	/**
	 * Verifies a float input
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a float, false if not
	 */
	protected function verifyFloat($input) {
		if(strval(floatval($input)) === strval($input)) {
			return(true);
		}
		return(false);
	}
	
	/**
	 * Verifies a positive float input (not negative, not zero)
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a positive float, false if not
	 */
	protected function verifyPositiveFloat($input) {
		if(floatval($input) <= 0) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a non-negative float input (positive or zero)
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a non-negative float, false if not
	 */
	protected function verifyNonNegativeFloat($input) {
		if(floatval($input) >= 0) {
			return(true);
		}
		return(false);
	}
	
	/**
	 * Verifies a string input
	 * @param mixed $input value to verify
	 * @return boolean true if the value is a string, false if not
	 */
	protected function verifyString($input) {
		return(is_string($input));
	}
	
	/**
	 * Verifies a minimum and/or maximum string length for the input
	 * @param string $input value to verify
	 * @param int|null minimum string length, null if no minimum string length
	 * @param int|null maximum string length, null if no maximum string length
	 * @return boolean true if the value long enough and/or not too long, false if not
	 */
	protected function verifyStringLength($input, $min = null, $max = null) {
		if(($min !== null) && (strlen($input) < intval($min))) {
			return(false);
		}
		if(($max !== null) && (strlen($input) > intval($max))) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a string is one of the given possible strings
	 * @param string $input value to verify
	 * @param array $map the valid strings
	 * @return boolean true if the string matches one of the valid strings, false if not
	 */
	protected function verifyStringAgainstMap($input, $map) {
		$found = array_search($input, $map);
		if($found !== false) {
			return(true);
		}
		return(false);
	}
	
	/**
	 * Verifies a character name
	 * @param string $input value to verify
	 * @return boolean true if the value is a valid character name, false if not
	 */
	protected function verifyCharacterName($input) {
		if(preg_match('/[^'.self::ALLOWED_CHARS_CHARACTERNAME.']/', $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a guild name
	 * @param string $input value to verify
	 * @return boolean true if the value is a valid guild name, false if not
	 */
	protected function verifyGuildName($input) {
		if(preg_match('/[^'.self::ALLOWED_CHARS_GUILDNAME.']/', $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a file name
	 * @param string $input value to verify
	 * @return boolean true if the value is a valid file name, false if not
	 */
	protected function verifyFileName($input) {
		if(preg_match('/[^' . self::ALLOWED_CHARS_FILENAME . ']/', $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a file path
	 * @param string $input value to verify
	 * @return boolean true if the value is a valid file name, false if not
	 */
	protected function verifyFilePath($input) {
		if(preg_match('/[^' . self::ALLOWED_CHARS_FILEPATH . ']/', $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a key string
	 * @param string $input value to verify
	 * @return boolean true if the value is a valid key string, false if not
	 */
	protected function verifyKey($input) {
		if(preg_match('/[^' . self::ALLOWED_CHARS_KEY . ']/', $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies a maximum of consecutive repeated specific/generic chars for the
	 * input
	 * @param string $input value to verify
	 * @param integer $max maximum allowed consecutive repeats
	 * @param string $chars regex expression char selector, default is any char
	 * @return boolean false if there are too many repetitions, true if not
	 */
	protected function verifyMaxRepeatedChars($input, $max, $chars = '.') {
		$max = intval($max);
		if($max < 1) {
			$max = 1;
		}
		$regex = '/('.$chars.')'.str_repeat('\\1', $max).'/';
		if(preg_match($regex, $input)) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Verifies an image string
	 * @param string $input value to verify
	 * @return boolean true if the input is a valid image data string, false if not
	 */
	protected function verifyImageString($input) {
		try {
			$test = @imagecreatefromstring($input);
			if($test === false) {
				return(false);
			}
		} catch (Exception $e) {
			return(false);
		}
		return(true);
	}
	
	/**
	 * Cleans an input for a data id recursively according to the cleaning rules
	 * defined in the meta data
	 * @param string $key data id
	 * @param mixed $value value to clean
	 * @param mixed $meta for meta data subtree
	 * @return mixed the cleaned value
	 */
	protected function cleanInput($key, $value, $meta) {
		$v = $value;
		if(isset($meta[$key]['subset']) && is_array($meta[$key]['subset']) && (!empty($meta[$key]['subset']))) {
			if(!is_array($v)) {
				return(array());
			}
			foreach($value as $subkey => $subvalue) {
				$v[$subkey] = $this->cleanInput($subkey, $subvalue, $meta[$key]['subset']);
			}
		} else {
			if(isset($meta[$key]['clean'])) {
				$clean = $meta[$key]['clean'];
				foreach($clean as $step) {
					foreach($step['params'] as &$param) {
						if($param === self::INPUT_TOKEN) {
							$param = $v;
						}
					}
					unset($param);
					$v = call_user_func_array(array($this, $step['method']), $step['params']);
				}
			}
		}
		return($v);
	}
	
	/**
	 * Cleans an input to be a boolean value
	 * @param mixed $input value to clean
	 * @return boolean
	 */
	protected function cleanBoolean($input) {
		$r = $input;
		$r = $r == true;
		return($r);
	}
	
	/**
	 * Cleans an input to be an integer value
	 * @param mixed $input value to clean
	 * @return integer
	 */
	protected function cleanInteger($input) {
		$r = $input;
		$r = intval($r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a non-negative integer value
	 * @param mixed $input value to clean
	 * @return integer
	 */
	protected function cleanNonNegativeInteger($input) {
		$r = $input;
		$r = $this->cleanInteger($r);
		if($r < 0) {
			$r = 0;
		}
		return($r);
	}
	
	/**
	 * Cleans an input to be a positive integer value
	 * @param mixed $input value to clean
	 * @return integer
	 */
	protected function cleanPositiveInteger($input) {
		$r = $input;
		$r = $this->cleanInteger($r);
		if($r < 1) {
			$r = 1;
		}
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid integer id
	 * @param mixed $input value to clean
	 * @return integer
	 */
	protected function cleanId($input) {
		$r = $input;
		return($this->cleanPositiveInteger($r));
	}
	
	/**
	 * Cleans an input to be a float value
	 * @param mixed $input value to clean
	 * @return float
	 */
	protected function cleanFloat($input) {
		$r = $input;
		$r = floatval($r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a non-negative float value
	 * @param mixed $input value to clean
	 * @return float
	 */
	protected function cleanNonNegativeFloat($input) {
		$r = $input;
		$r = $this->cleanFloat($r);
		if($r < 0.0) {
			$r = 0.0;
		}
		return($r);
	}
	
	/**
	 * Cleans an input to be a positive float value
	 * @param mixed $input value to clean
	 * @return float
	 */
	protected function cleanPositiveFloat($input) {
		$r = $input;
		$r = $this->cleanFloat($r);
		if($r <= 0.0) {
			$r = 1.0;
		}
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid item name
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanItemName($input) {
		$r = $input;
		$r = trim($r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid guild name
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanGuildName($input) {
		$r = $input;
		$r = trim($r);
		$r = preg_replace('/[^'.self::ALLOWED_CHARS_GUILDNAME.']/', '', $r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid character name
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanCharacterName($input) {
		$r = $input;
		$r = trim($r);
		$r = preg_replace('/[^'.self::ALLOWED_CHARS_CHARACTERNAME.']/', '', $r);
		$r = mb_convert_case($r, MB_CASE_TITLE);
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid file name
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanFileName($input) {
		$r = $input;
		$r = trim($r);
		$r = preg_replace('/[^'.self::ALLOWED_CHARS_FILENAME.']/', '', $r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid file path
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanFilePath($input) {
		$r = $input;
		$r = trim($r);
		$r = preg_replace('/[^'.self::ALLOWED_CHARS_FILEPATH.']/', '', $r);
		return($r);
	}
	
	/**
	 * Cleans an input to be a valid key
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanKey($input) {
		$r = $input;
		$r = trim($r);
		$r = preg_replace('/[^'.self::ALLOWED_CHARS_KEY.']/', '', $r);
		return($r);
	}
	
	/**
	 * Cleans an input to be free of html tags
	 * @param mixed $input value to clean
	 * @return string
	 */
	protected function cleanNoHTML($input) {
		$r = $input;
		$r = trim($r);
		$r = strip_tags($r);
		return($r);
	}
}

/**
 * Generic data class that provides meta data for most other WowData subclasses
 * @package wow-data-access
 * @subpackage wow-data
 */
abstract class WowDataBase extends WowData {
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array_merge(parent::getMetaData(), array(
			// --- locale ---------------------------------------- R -
			'locale' => array(
				'input'    => true,
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Data locale key must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Data locale key must not contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'locale',
			),
			// --- region ---------------------------------------- R -
			'region' => array(
				'input'    => true,
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Data region key must not be empty.'),
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Data region key must not contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'region',
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