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
 * WowDataAccess.lib.php, 
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataAccess.lib.php

/**
 * Core class that interfaces with the implementing system
 * @package wow-data-access
 */
class WowDataAccess {
	/**
	 * Singleton instance
	 * @var WowDataAccess
	 */
	private static $singleton = null;
	
	/**
	 * Locale definition
	 * @var string
	 */
	const LOCALE_EN_US = 'en_US';
	const LOCALE_ES_MX = 'es_MX';
	const LOCALE_DE_DE = 'de_DE';
	const LOCALE_EN_GB = 'en_GB';
	const LOCALE_ES_ES = 'es_ES';
	const LOCALE_FR_FR = 'fr_FR';
	const LOCALE_RU_RU = 'ru_RU';
	const LOCALE_KO_KR = 'ko_KR';
	const LOCALE_ZH_TW = 'zh_TW';
	const LOCALE_ZH_CN = 'zh_CN';
	
	/**
	 * Region definition
	 * @var string
	 */
	const REGION_US = 'us';
	const REGION_EU = 'eu';
	const REGION_KR = 'kr';
	const REGION_TW = 'tw';
	const REGION_CN = 'cn';
		
	/**
	 * Parameter map
	 * @var array
	 */
	protected $paramsValid = array(
		'locale'    => array('type' => 'string'),
		'region'    => array('type' => 'string'),
		'realm'     => array('type' => 'string', 'prepare' => true),
		'guild'     => array('type' => 'string'),
		'character' => array('type' => 'string'),
		'slotid'    => array('type' => 'int'),
		'itemid'    => array('type' => 'int'),
		'icon'      => array('type' => 'string'),
		'filename'  => array('type' => 'string'),
		'fields'    => array('type' => 'array.string'),
	);
	
	/**
	 * Configuration
	 * @var array
	 */
	protected $config = array(
		'logging'       => false,
		'loggingDirect' => true,
	);
	
	/**
	 * Default settings
	 * @var array
	 */
	protected $defaults = array(
		'locale' => self::LOCALE_DE_DE,
		'region' => self::REGION_EU,
		'realm'  => 'Gilneas',
	);
	
	/**
	 * Registered WowDataChannel subclasses
	 * @var array
	 */
	protected $channels = array();
	
	/**
	 * Log entries
	 * @var array
	 */
	protected $log = array();
	
	/**
	 * Singleton fetching
	 * @params array $params the initialization parameters
	 */
	static function obj(array $params = array()) {
		if(self::$singleton == null) {
			self::$singleton = new WowDataAccess();
			self::$singleton->init($params);
		}
		return(self::$singleton);
	}
	
	/**
	 * Exception handling
	 * @params string $message additional text to display upon throwing the error
	 * @params integer $code error code to provide
	 */
	public function error($message, $code = 0) {
		$text = '[[[ ' . __CLASS__ . ' - Error: ' . $message . ' ]]]';
		throw new Exception($text, $code);
	}
	
	/**
	 * Logging
	 * @params string $message text to output to the log
	 */
	public function log($message = null) {
		if($this->config['logging'] === true) {
			if($message === null) {
				if($this->config['loggingDirect'] !== true) {
					return($this->log);
				}
			} else {
				if($this->config['loggingDirect'] === true) {
					echo('<pre style="padding:0.5em; margin:1em; border:2px solid #999; background-color:#ddd;"><div style="background-color:#bbb; padding: 0.25em;">'.date('H:i:s:u - d.m.Y', time()).'</div>'.$message.'</pre>');
					return(true);
				} else {
					return($this->log[] = array(
						'time'    => time(),
						'message' => $message
					));
				}
			}
		}
		return(null);
	}
	
	/**
	 * Constructor that will overwrite defaults and register the WowDataChannel
	 * subclasses
	 * @param array $params option array that will be processed
	 */
	protected function init(array $params = array()) {
		/**
		 * Overwrite config
		 */
		if(!empty($params['config'])) {
			foreach($this->config as $key => $value) {
				if(isset($params['config'][$key])) {
					$this->config[$key] = $params['config'][$key];
				}
			}
		}
		
		/**
		 * Overwrite defaults
		 */
		if(!empty($params['defaults'])) {
			foreach($this->defaults as $key => $value) {
				if(isset($params['defaults'][$key])) {
					$this->defaults[$key] = strval($params['defaults'][$key]);
				}
			}
		}
		
		/**
		 * Register the WowDataChannel subclasses
		 */
		if(!empty($params['channels'])) {
			if(!$this->helperIsCollectorArray($params['channels'])) {
				$params['channels'] = array($params['channels']);
			}
			foreach($params['channels'] as $channelid => $channeldata) {
				if(isset($channeldata['class']) || !empty($channeldata['class'])) {
					$channel = $channeldata['class'];
				} else {
					WowDataAccess::obj()->error(__METHOD__.' >>> Channel registration parameter "class" is missing!', 500);
					return(false);
				}
				if(isset($channeldata['options'])) {
					$options = $channeldata['options'];
				} else {
					$options = array();
				}
				if(!$this->registerWowDataChannel(strval($channel), $options)) {
					WowDataAccess::obj()->error(__METHOD__.' >>> Unknown error while registering channel '.$channelid.' ('.$channel.')!', 500);
					return(false);
				}
			}
		} else {
			WowDataAccess::obj()->error(__METHOD__.' >>> No WowDataChannels found!', 500);
			return(false);
		}
		return(true);
	}
	
	/**
	 * Constructor
	 */
	protected function __construct() {
		// Do nothing here...
	}
	
	/**
	 * Deny cloning of objects
	 * @return null
	 */
	private function __clone() {}
	
	/**
	 * Registers a WowDataChannel subclass
	 * @param string $channel WowDataChannel subclass
	 * @param array $options channel options
	 * @return boolean true on successful registration, false on failure
	 */
	protected function registerWowDataChannel($channel, array $options = array()) {
		if(class_exists($channel)) {
			if(!is_subclass_of($channel, 'WowDataChannel')) {
				WowDataAccess::obj()->error('Specified channel class "'.$channel.'" is no sublclass of "WowDataChannel"!', 500);
			} else {
				return($this->channels[] = new $channel($options));
			}
		} else {
			WowDataAccess::obj()->error('Channel class "'.$channel.'" does not exist!', 500);
		}
		return(false);
	}
	
	/**
	 * Merges whitelisted input parameters with default parameters
	 * @param array $params custom parameters
	 * @return array merged parameters
	 */
	protected function mergeParams(array $params) {
		if($this->helperIsCollectorArray($params)) {
			$pc = array();
			foreach($params as $subkey => $subparams) {
				$p = $this->defaults;
				foreach($this->paramsValid as $pkey => $popt) {
					if(!empty($subparams[$pkey])) {
						if(!isset($popt['type'])) {
							WowDataAccess::obj()->error(__METHOD__.' >>> Option "type" for parameter "'.$pkey.'" is missing!', 500);
							return(false);
						}
						switch($popt['type']) {
							case 'int':
								$p[$pkey] = intval($subparams[$pkey]);
								break;
							case 'string':
								$p[$pkey] = strval($subparams[$pkey]);
								break;
							case 'array.int':
							case 'array.string':
								if(!is_array($subparams[$pkey])) {
									$subparams[$pkey] = array($subparams[$pkey]);
								}
								$p[$pkey] = $subparams[$pkey];
								break;
							default:
								WowDataAccess::obj()->error(__METHOD__.' >>> Unknown value for option "type": "'.$popt['type'].'"!', 500);
								return(false);
								break;
						}
						if(isset($p[$pkey])) {
							$methodname = 'prepareParam'.mb_convert_case($pkey, MB_CASE_TITLE);
							if(isset($popt['prepare']) && ($popt['prepare'] === true) && method_exists($this, $methodname)) {
								$p[$pkey] = call_user_func(array($this, $methodname), $p[$pkey]);
							}
						}
					}
				}
				$pc[] = $p;
			}
			return($pc);
		} else {
			$p = $this->defaults;
			foreach($this->paramsValid as $pkey => $popt) {
				if(!empty($params[$pkey])) {
					if(!isset($popt['type'])) {
						WowDataAccess::obj()->error(__METHOD__.' >>> Option "type" for parameter "'.$pkey.'" is missing!', 500);
						return(false);
					}
					switch($popt['type']) {
						case 'int':
							$p[$pkey] = intval($params[$pkey]);
							break;
						case 'string':
							$p[$pkey] = strval($params[$pkey]);
							break;
						case 'array.int':
						case 'array.string':
							if(!is_array($params[$pkey])) {
								$params[$pkey] = array($params[$pkey]);
							}
							$p[$pkey] = $params[$pkey];
							break;
						default:
							WowDataAccess::obj()->error(__METHOD__.' >>> Unknown value for option "type": "'.$popt['type'].'"!', 500);
							return(false);
							break;
					}
					if(isset($p[$pkey])) {
						$methodname = 'prepareParam'.mb_convert_case($pkey, MB_CASE_TITLE);
						if(isset($popt['prepare']) && ($popt['prepare'] === true) && method_exists($this, $methodname)) {
							$p[$pkey] = call_user_func(array($this, $methodname), $p[$pkey]);
						}
					}
				}
			}
			return($p);
		}
	}
	
	/**
	 * Iterating over parameters and returned WowData subclass objects to find out
	 * which parameters did not yield a result
	 * @param array $params parameter array
	 * @param array $wdos result array of WowData subclass objects
	 * @return array contains all missing parameter groups
	 */
	protected function getUnfoundParamGroups($params, $wdos) {
		// WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r($wdos, true));
		$c = array();
		foreach($params as $pkey => $pparams) {
			$match = false;
			foreach($wdos as $wkey => $wdo) {
				if((is_subclass_of($wdo, 'WowData')) && ($wdo->isValid()) && $wdo->matchParams($pparams)) {
					$match = true;
				}
			}
			if($match == false) {
				$c[$pkey] = $pparams;
			}
		}
		return($c);
	}
	
	/**
	 * Iterating over WowData subclass results obejcts and returns the object that
	 * matches the given parameter group
	 * @param array $wdos WowData subclass objects
	 * @param array $params parameter group
	 * @return mixed the matching WowData subclass object or false if no object matches
	 */
	protected function getWdoForParams($wdos, $params) {
		foreach($wdos as $wdo) {
			if(is_subclass_of($wdo, 'WowData')) {
				if($wdo->matchParams($params)) {
					return($wdo);
				}
			}
		}
	}
	
	/**
	 * Iterating over the registered channels trying to find the data
	 * @param string $wd WowData subclass to perform lookup on
	 * @param array $params query parameters
	 * @return mixed|false if data is found returns the corresponding WowData subclass object, false if not
	 */
	protected function lookupData($wd, $params) {
		WowDataAccess::obj()->log('START LOOKUP: '.$wd);
		$multiMode = $this->helperIsCollectorArray($params);
		if(!$multiMode) {
			$params = array($params);
		}
		$writeback = array();
		$data = array();
		foreach($params as $pkey => $pparams) {
			$data[$pkey] = false;
		}
		$tparams   = $params;
		foreach($this->channels as $channel) {
			$result = $channel->handleWowData($wd, 'read', $tparams);
			if(!empty($result)) {
				foreach($params as $pkey => $pparams) {
					$wdo = $this->getWdoForParams($result, $pparams);
					if(($wdo !== false) && is_subclass_of($wdo, 'WowData') && $wdo->isValid()) {
						$data[$pkey] = $wdo;
					}
				}
				$searchcount = count($params);
				$findcount   = count($result);
				WowDataAccess::obj()->log('Reading of '.$wd.' in Channel '.get_class($channel).' was successful ('.$findcount.'/'.$searchcount.')!');
				$tparams = $this->getUnfoundParamGroups($tparams, $result);
				if(!empty($tparams)) {
					WowDataAccess::obj()->log('One or more param groups were not found: '.print_r($tparams, true));
				}
				if(empty($tparams)) {
					break;
				} else {
					array_unshift($writeback, array(
						'channel' => $channel,
						'keys'    => array_keys($tparams),
					));
				}
			} else {
				WowDataAccess::obj()->log('Reading '.$wd.' in Channel '.get_class($channel).' failed!');
				array_unshift($writeback, array(
					'channel' => $channel,
					'keys'    => array_keys($tparams),
				));
			}
		}
		
		/**
		 * If there are writeable channels that were passed without success, save the data back into them
		 */
		if(!empty($writeback) && (!empty($data))) {
			foreach($writeback as $wbdata) {
				$tdata = array();
				foreach($wbdata['keys'] as $key) {
					$tdata[$key] = $data[$key];
				}
				if(!empty($tdata)) {
					$result = $wbdata['channel']->handleWowData($wd, 'write', $tdata);
					if(is_array($result)) {
						foreach($result as $r) {
							if($r === true) {
								$result = true;
								break;
							}
						}
					}
					if($result === true) {
						WowDataAccess::obj()->log('Writing '.$wd.' in Channel '.get_class($wbdata['channel']).' was successful!');
					} else {
						WowDataAccess::obj()->log('Writing '.$wd.' in Channel '.get_class($wbdata['channel']).' failed!');
					}
				}
			}
		}
		if($multiMode) {
			return($data);
		} else {
			return(array_shift($data));
		}
	}
	
	/**
	 * Perform a realm lookup
	 * @param array $params parameters to use when searching for the realm
	 * @return WowDataRealm|false WowDataRealm object if found, false if not
	 */
	public function openRealm(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataRealm', $p));
	}
	
	/**
	 * Perform an item lookup
	 * @param array $params parameters to use when searching for the item
	 * @return WowDataItem|false WowDataItem object if found, false if not
	 */
	public function openItem(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataItem', $p));
	}
	
	/**
	 * Perform an equipped item lookup
	 * @param array $params parameters to use when searching for the item
	 * @return WowDataItemEquipped|false WowDataItemEquipped object if found, false if not
	 */
	public function openItemEquipped(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataItemEquipped', $p));
	}
	
	/**
	 * Perform a character lookup
	 * @param array $params parameters to use when searching for the item
	 * @return WowDataCharacter|false WowDataCharacter object if found, false if not
	 */
	public function openCharacter(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataCharacter', $p));
	}
	
	/**
	 * Perform a character thumbnail lookup
	 * @param array $params parameters to use when searching for the item
	 * @return WowDataCharacterThumbnail|false WowDataCharacterThumbnail object if found, false if not
	 */
	public function openCharacterThumbnail(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataCharacterThumbnail', $p));
	}
	
	/**
	 * Perform a guild lookup
	 * @param array $params parameters to use when searching for the item
	 * @return WowDataGuild|false WowDataGuild object if found, false if not
	 */
	public function openGuild(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataGuild', $p));
	}
	
	/**
	 * Perform an icon lookup
	 * @param array $params parameters to use when searching for the icon
	 * @return WowDataIcon|false WowDataIcon object if found, false if not
	 */
	public function openIcon(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataIcon', $p));
	}
	
	/**
	 * Perform an item render lookup
	 * @param array $params parameters to use when searching for the item render
	 * @return WowDataItemRender|false WowDataItemRender object if found, false if not
	 */
	public function openItemRender(array $params) {
		$p = $this->mergeParams($params);
		return($this->lookupData('WowDataItemRender', $p));
	}
	
	/**
	 * Checks if an input is an array without string keys
	 * @param mixed $input value to check
	 * @return boolean true if an array without string keys was found, false if not
	 */
	public function helperIsCollectorArray($input) {
		if(is_array($input)) {
			return(count(array_filter(array_keys($input), 'is_string')) == 0);
		}
		return(false);
	}
	
	/**
	 * Prepare a realm input parameter
	 * @param string $param realm name that will be prepared
	 * @return string the prepared realm slug
	 */
	protected function prepareParamRealm($param) {
		
		// Remove any quote signs and regular dashes
		$param = preg_replace('~\'|"|-~su', '' , $param);
		
		// replace non letter or digits by dashes
		$param = preg_replace('~[^\\pL\d]+~u', '-', $param);
		
		// trim
		$param = trim($param, '-');
		
		/*
		// transliterate
		if(function_exists('iconv')) {
			$param = iconv('utf-8', 'us-ascii//TRANSLIT', $param);
		}
		// */
		
		// lowercase
		$param = strtolower($param);
		
		// remove unwanted characters
		$param = preg_replace('~[^-\w]+~', '', $param);
		
		// return default realm slug
		if(empty($param))
		{
			return 'n-a';
		}
		
		return($param);
		
		/*
		// Remove any quote signs
		$param = preg_replace('~\'|"|-~su'  , '' , $param);
		
		// Replace any underscore or non-word characters with dashes
		$param = preg_replace('@[^\w]|_@siu' , '-', $param);
		
		// Replace multiple consecutive dashes with a single one
		$param = preg_replace('@[\-]{2,}@siu', '-', $param);
		
		// Lower case the whole name
		$param = mb_strtolower($param);
		
		return($param);
		// */
	}
	
}

?>