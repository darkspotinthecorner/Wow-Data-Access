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
 * WowDataChannelFilesystem.class.php, Data channel definition for filesystem access
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataChannelFilesystem.class.php

/**
 * Data channel definition for filesystem access
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
class WowDataChannelFilesystem extends WowDataChannel {
	/**
	 * Array that contains the options and may be filled with defaults
	 * @var array
	 */
	protected $options = array(
		'filesystem' => array(
			'basePath' => 'WowDataChannelFilesystem/',
		),
		'schemes' => array(
			'WowDataRealm' => array(
				'extPath'  => '{region}/realms/',
				'fileName' => '{realm}.txt',
				'lifetime' => 300, // 5 minutes
			),
			'WowDataItem' => array(
				'extPath'  => '{region}/items/',
				'fileName' => '{itemid}.{locale}.txt',
				'lifetime' => 2592000, // 30 days
			),
			'WowDataItemEquipped' => array(
				'extPath'  => '{region}/characters/realms/{realm}/',
				'fileName' => '{character}.{slotid}.{itemid}.{locale}.txt',
				'lifetime' => 259200, // 3 days
			),
			'WowDataCharacter' => array(
				'extPath'  => '{region}/characters/realms/{realm}/',
				'fileName' => '{character}.{locale}.txt',
				'lifetime' => 86400, // 1 day
			),
			'WowDataCharacterThumbnail' => array(
				'extPath'  => '{region}/characters/thumbnails/',
				'fileName' => '{filename}',
				'lifetime' => 2592000, // 30 days
			),
			'WowDataGuild' => array(
				'extPath'  => '{region}/guilds/realms/{realm}/',
				'fileName' => '{guild}.{locale}.txt',
				'lifetime' => 86400, // 1 day
			),
			'WowDataIcon' => array(
				'extPath'  => '{region}/icons/',
				'fileName' => '{icon}.jpg',
			),
		),
	);
	
	/**
	 * Tries to find a data object specified by the method in $method and data in $params
	 * @param string $method the method that will identify the WowData subclass used
	 * @param array $params access parameters like 'locale', 'region' or 'realm'
	 * @return mixed Appropriate WowData subclass on success or false if nothing was found
	 */
	public function openRealm($wd, $params) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, $params);
		if(file_exists($uri)) {
			if(!isset($opt['lifetime']) || (isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) > time()))) {
				$realm = unserialize(file_get_contents($uri));
				return($realm);
			}
		}
		return(false);
	}
	
	/**
	 * Tries to find a data object specified by the method in $method and data in $params
	 * @param string $method the method that will identify the WowData subclass used
	 * @param array $params access parameters like 'locale', 'region' or 'realm'
	 * @return mixed Appropriate WowData subclass on success or false if nothing was found
	 */
	public function openIcon($wd, $params) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, $params);
		if(file_exists($uri)) {
			if(!isset($opt['lifetime']) || (isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) > time()))) {
				$imgdata = file_get_contents($uri);
				$icon    = new WowDataIcon(array(
					'region' => $params['region'],
					'icon'   => $params['icon'],
					'image'  => $imgdata,
				));
				return($icon);
			}
		}
		return(false);
	}
	
	/**
	 * Tries to find a data object specified by the method in $method and data in $params
	 * @param string $method the method that will identify the WowData subclass used
	 * @param array $params access parameters like 'locale', 'region' or 'realm'
	 * @return mixed Appropriate WowData subclass on success or false if nothing was found
	 */
	public function openCharacterThumbnail($wd, $params) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, $params);
		if(file_exists($uri)) {
			if(!isset($opt['lifetime']) || (isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) > time()))) {
				$imgdata   = file_get_contents($uri);
				$thumbnail = new WowDataCharacterThumbnail(array(
					'region'   => $params['region'],
					'filename' => $params['filename'],
					'image'    => $imgdata,
				));
				return($thumbnail);
			}
		}
		return(false);
	}
	
	/**
	 * Saves the WowDataRealm object directly in the filesystem
	 * @param mixed $wdo WowData subclass object to save
	 * @return boolean true on success, false on failure
	 */
	protected function saveRealm($wd, $wdo) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, array(
			'region' => $wdo->get('region'),
			'realm'  => $wdo->get('slug'),
		));
		if(!file_exists($uri)) {
			file_put_contents($uri, serialize($wdo));
			return(true);
		} else {
			if(isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) <= time())) {
				file_put_contents($uri, serialize($wdo));
				return(true);
			}
		}
		return(false);
	}
	
	/**
	 * Saves the image in a WowDataIcon object directly in the filesystem
	 * @param mixed $wdo WowData subclass object to save
	 * @return boolean true on success, false on failure
	 */
	protected function saveIcon($wd, $wdo) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, array(
			'region' => $wdo->get('region'),
			'icon'   => $wdo->get('icon'),
		));
		if(!file_exists($uri)) {
			file_put_contents($uri, $wdo->get('image'));
			return(true);
		} else {
			if(isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) <= time())) {
				file_put_contents($uri, $wdo->get('image'));
				return(true);
			}
		}
		return(false);
	}
	
	/**
	 * Saves the WowDataCharacterThumbnail object directly in the filesystem
	 * @param mixed $wdo WowData subclass object to save
	 * @return boolean true on success, false on failure
	 */
	protected function saveCharacterThumbnail($wd, $wdo) {
		$opt = $this->getWowDataClassOptions($wd);
		$uri = $this->buildUri($wd, array(
			'region' => $wdo->get('region'),
			'realm'  => $wdo->get('slug'),
		));
		if(!file_exists($uri)) {
			file_put_contents($uri, serialize($wdo));
			return(true);
		} else {
			if(isset($opt['lifetime']) && ((filemtime($uri) + $opt['lifetime']) <= time())) {
				file_put_contents($uri, serialize($wdo));
				return(true);
			}
		}
		return(false);
	}
	
	/**
	 * Registers the WowData subclass handlers
	 */
	protected function initRegisterWowDataHandlers() {
		$this->registerWowDataHandler('WowDataRealm',              'read',  array('multiSupport' => false, 'handler' => 'openRealm'));
		$this->registerWowDataHandler('WowDataRealm',              'write', array('multiSupport' => false, 'handler' => 'saveRealm'));
		$this->registerWowDataHandler('WowDataIcon',               'read',  array('multiSupport' => false, 'handler' => 'openIcon'));
		$this->registerWowDataHandler('WowDataIcon',               'write', array('multiSupport' => false, 'handler' => 'saveIcon'));
		$this->registerWowDataHandler('WowDataCharacterThumbnail', 'read',  array('multiSupport' => false, 'handler' => 'openCharacterThumbnail'));
		$this->registerWowDataHandler('WowDataCharacterThumbnail', 'write', array('multiSupport' => false, 'handler' => 'saveCharacterThumbnail'));
	}
	
	/**
	 * Perform basic checks on the channel options iteself
	 * @return boolean true success, false on failure
	 */
	protected function initChecksCore() {
		/**
		 * Prebuild the base path
		 */
		if(isset($this->options['filesystem']['basePath']) && !empty($this->options['filesystem']['basePath'])) {
			$this->options['filesystem']['basePath'] = dirname(__FILE__) . '/' . $this->options['filesystem']['basePath'];
		}
		
		/**
		 * Checks if the base path is set
		 */
		if(!isset($this->options['filesystem']['basePath']) || empty($this->options['filesystem']['basePath'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (filesystem/basepath) is missing or empty!', 500);
			return(false);
		}
		
		/**
		 * Checks if the base path exists
		 */
		if(!is_dir($this->options['filesystem']['basePath'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Option "filesystem/basepath" ('.$this->options['filesystem']['basePath'].') does not point to a valid directory!', 500);
			return(false);
		}
		
		/**
		 * Checks if the base path is writeable
		 */
		if(!is_writable($this->options['filesystem']['basePath'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Option "filesystem/basepath" ('.$this->options['filesystem']['basePath'].') must be a writeable directory!', 500);
			return(false);
		}
		
		return(true);
	}
	
	/**
	 * Registers a WowDataClass for this channel. Checks the parameters given for
	 * the data class and verifies them against the mysql table and the data class
	 * meta data
	 * @param String $wd class name of the WowData subclass
	 * @param array $options associative array containing the data needed to connect this channel to the WowData subclass
	 * @return boolean true on success, false on failure
	 */
	protected function initChecksWowDataScheme($wd, $options = array()) {
		/**
		 * Check if the scheme has an extension path defined
		 */
		if(!isset($options['extPath']) || empty($options['extPath'])) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/extPath) is missing or empty!', 500);
			return(false);
		}
		
		/**
		 * Check if the scheme has a file name defined
		 */
		if(!isset($options['fileName']) || empty($options['fileName'])) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/fileName) is missing or empty!', 500);
			return(false);
		}
		
		/**
		 * Check if the scheme has a lifetime that is valid
		 */
		if(isset($options['lifetime'])) {
			$options['lifetime'] = intval($options['lifetime']);
			if($options['lifetime'] < 1) {
				WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Option "schemes/'.$wd.'/lifetime" ('.$options['lifetime'].') must be a positive integer!', 500);
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 * Builds a filesystem uri from the options with the given parameters
	 * @param string $wd WowData subclass (scheme) to build for
	 * @param string $params associative array that contains the parameters that will be parsed into the uri
	 * @return string|false url on success, false on failure
	 */
	protected function buildUri($wd, array $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$opt = $this->getWowDataClassOptions($wd);
			if(!empty($opt['basePath'])) {
				$uri = $opt['basePath'];
			} else {
				if(!empty($opt['extPath'])) {
					$uri = $this->options['filesystem']['basePath'] . $opt['extPath'];
				} else {
					$uri = $this->options['filesystem']['basePath'];
				}
			}
			$matches = array();
			if(preg_match_all('@\{([\w]+)\}@siu', $uri, $matches)) {
				if(isset($matches[1])) {
					foreach($matches[1] as $fp) {
						if(isset($params[$fp])) {
							$uri = str_replace(('{'.$fp.'}'), $this->prepareUriParameter($fp, $params[$fp]), $uri);
						}
					}
				}
			}
			$this->prepareUri($uri);
			$uri = $uri . $opt['fileName'];
			if(preg_match_all('@\{([\w]+)\}@siu', $uri, $matches)) {
				if(isset($matches[1])) {
					foreach($matches[1] as $fp) {
						if(isset($params[$fp])) {
							$uri = str_replace(('{'.$fp.'}'), $this->prepareUriParameter($fp, $params[$fp]), $uri);
						}
					}
				}
			}
			return($uri);
		}
		return(false);
	}	
	
	/**
	 * Checks
	 * @param string $key uri parameter key
	 * @param string $value uri parameter value
	 * @return mixed processed parameter value
	 */
	protected function prepareUri($uri) {
		$path = rtrim($uri, '/');
		if(!file_exists($path)) {
			mkdir($path, 0777, true);
		}
	}
	
	/**
	 * Performs a context sensitive cleaning and pre-processing of uri parameters
	 * @param string $key uri parameter key
	 * @param string $value uri parameter value
	 * @return mixed processed parameter value
	 */
	protected function prepareUriParameter($key, $value) {
		$work = $value;
		switch($key) {
			case 'locale':
				$work = strtolower(trim($work));
				$work = substr($work, 0, 2);
				break;
			case 'region':
				$work = strtolower(trim($work));
				break;
			case 'realm':
				$work = strtolower(trim($work));
				$work = preg_replace('/[^a-zA-Z ]/', '', $work);
				$work = preg_replace('/ /', "-", $work);
				break;
			case 'character':
				$work = strtolower(trim($work));
				break;
			case 'slotid':
			case 'itemid':
				$work = intval($work);
				break;
			case 'icon':
				$work = strtolower(trim($work));
				break;
		}
		return($work);
	}
	
}

?>