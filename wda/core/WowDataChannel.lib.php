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
 * WowDataChannel.lib.php, Data channel basic classes that are used by the
 * actual channel definitions
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataChannel.lib.php

/**
 * Basic data channel that implements the core methods used by all data channels
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
abstract class WowDataChannel {
	/**
	 * Registered WowData subclasses and their settings
	 * @var array
	 */
	protected $registeredWowDataClasses = array();
	
	/**
	 * Registered WowData handlers
	 * @var array
	 */
	protected $registeredWowDataHandlers = array();
	
	/**
	 * Default channel options
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * Constructor that will generate the channel's options and initialize the data
	 * channel
	 * @param array $params option array that will be merged with the channel's default options
	 * @return boolean return of init()
	 */
	public function __construct($params) {
		$this->options = $this->mergeOptions($this->options, $params);
		$this->init();
	}
	
	/**
	 * Uses the options to initialize the channel, this method should verify the
	 * input
	 * @return boolean true if success, false if the init failed
	 */
	protected function init() {
		/**
		 * Register the WowData subclass handlers
		 */
		$this->initRegisterWowDataHandlers();
		
		/**
		 * Perform the core checks
		 */
		if(!$this->initChecksCore()) {
			return(false);
		}
		
		/**
		 * Register the individual WowData subclass data
		 */
		if(isset($this->options['schemes']) && !empty($this->options['schemes'])) {
			foreach($this->options['schemes'] as $wd => $options) {
				$this->initRegisterWowDataClass($wd, $options);
			}
		} else {
			WowDataAccess::obj()->error('Initializing Error in "'.__CLASS__.'" - No schemes for handling WowData subclasses found"!', 500);
			return(false);
		}
		
		return(true);
	}
	
	/**
	 * Performs some generic checks that should be passed before a WowData subclass
	 * should be allowed to register itself
	 * @param String $wd class name of the WowData subclass
	 * @param array $options associative array containing the data needed to connect this channel to the WowData subclass
	 * @return boolean true on success, false on failure
	 */
	protected function initRegisterWowDataClass($wd, $options = array()) {
		/**
		 * Check if the scheme is deactivated
		 */
		if($options === false) {
			WowDataAccess::obj()->log('WowData subclass "'.$wd.'" was not registered in channel "'.get_class($this).'" because it is deactivated!');
			return(false);
		}
		
		/**
		 * Check if there are no handlers registered for this WowData subclass
		 */
		if(!$this->areWowDataHandlersRegistered($wd)) {
			WowDataAccess::obj()->log('WowData subclass "'.$wd.'" was not registered in channel "'.get_class($this).'" because there are no handlers for it!');
			return(false);
		}
		
		/**
		 * Perform the custom checks
		 */
		if($this->initChecksWowDataScheme($wd, $options)) {
			/**
			 * Call the parent class channel register method
			 */
			return($this->registerWowDataClass($wd, $options));
		}
		
		return(false);
	}
	
	/**
	 * Performs basic checks for the channel options
	 * @return void
	 */
	abstract protected function initChecksCore();
	
	/**
	 * Performs checks for the WowData subclass options
	 * @return void
	 */
	abstract protected function initChecksWowDataScheme($wd, $options = array());
	
	/**
	 * Registers the WowData subclass handlers for the data channel
	 * @return void
	 */
	abstract protected function initRegisterWowDataHandlers();
	
	/**
	 * Registers a WowDataClass for this channel
	 * @param String $wd class name of the WowData subclass
	 * @param array $options associative array containing the data needed to connect this channel to the WowData subclass
	 * @return boolean true on success, false on failure
	 */
	protected function registerWowDataClass($wd, $options = array()) {
		// Check if the given class is a subclass of WowData
		if(!is_subclass_of($wd, 'WowData')) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Given class "'.$wd.'" is no subclass of WowData!', 500);
			return(false);
		}
		if($options === false) {
			return(false);
		}
		return($this->registeredWowDataClasses[$wd] = $options);
	}
	
	/**
	 * Registers an anonymous function to handle a WowData operation
	 * @param String $wd class name of the WowData subclass
	 * @param String $mode 'read' or 'write' for query mode
	 * @param Array $params Handler function and parameters
	 * @return boolean true if the handler was registered, false if not
	 */
	protected function registerWowDataHandler($wd, $mode, $params) {
		if(($mode !== 'read') && ($mode !== 'write')) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Parameter $mode must be either "read" or "write"!', 500);
			return(false);
		}
		if(!isset($params['multiSupport'])) {
			$params['multiSupport'] = false;
		} else {
			$params['multiSupport'] = ($params['multiSupport'] === true);
		}
		if(!isset($params['handler'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option "handler" is missing!', 500);
			return(false);
		} else {
			if(!is_callable(array($this, $params['handler']))) {
				WowDataAccess::obj()->error(__METHOD__.' >>> Option "handler" must be a valid method!', 500);
				return(false);
			}
		}
		return($this->registeredWowDataHandlers[$wd][$mode][] = $params);
	}
	
	/**
	 * Unifies all given parameters to make them unique
	 * @param array $params Parameter groups or data objects
	 * @return array the unique-made array
	 */
	protected function unifyParams($params) {
		$c = array();
		foreach($params as $pparams) {
			if($pparams !== false) {
				if(is_subclass_of($pparams, 'WowData') && $pparams->isValid()) {
					$keybase = $pparams->get($pparams->getParamDataIdMap());
				} else {
					$keybase = $pparams;
				}
				$c[md5(implode('###', $keybase))] = $pparams;
			}
		}
		return($c);
	}
	
	/**
	 * Calls all handlers associated with the given WowData subclass and mode
	 * @param String $wd class name of the WowData subclass
	 * @param String $mode 'read' or 'write' for query mode
	 * @param mixed $params Query parameters
	 * @return boolean true on success, false on failure
	 */
	public function handleWowData($wd, $mode, $params) {
		// WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r(array('$wd' => $wd, '$node' => $mode, '$params' => $params), true));
		if($this->areWowDataHandlersRegistered($wd, $mode)) {
			$params   = $this->unifyParams($params);
			$handlers = $this->getWowDataHandlers($wd, $mode);
			foreach($handlers as $handler) {
				/**
				 * Checks if the handler supports multiple parameter groups
				 */
				if($handler['multiSupport'] === false) {
					$result = array();
					$method = $handler['handler'];
					foreach($params as $pparams) {
						$r = $this->$method($wd, $pparams);
						if($r !== false) {
							$result[] = $r;
						}
					}
					return($result);
				} else {
					$method = $handler['handler'];
					$result = $this->$method($wd, $params);
					return($result);
				}
			}
		}
		return(false);
	}
	
	/**
	 * Checks if a class is already registered
	 * @param String $wd class name of the WowData subclass
	 * @return boolean true if the class is registered, false if not
	 */
	protected function isWowDataClassRegistered($wd) {
		return(isset($this->registeredWowDataClasses[$wd]));
	}
	
	/**
	 * Fetches the options of a registered WowData subclass
	 * @param String $wd class name of the WowData subclass
	 * @return array|false option array on success, false on failure
	 */
	protected function getWowDataClassOptions($wd) {
		if($this->isWowDataClassRegistered($wd)) {
			return($this->registeredWowDataClasses[$wd]);
		}
		return(false);
	}
	
	/**
	 * Checks if a handler for a WowData subclass and mode is already registered
	 * @param String $wd class name of the WowData subclass
	 * @param String $mode 'read' or 'write' for query mode
	 * @return boolean true if the class is registered, false if not
	 */
	protected function areWowDataHandlersRegistered($wd, $mode = null) {
		if($mode === null) {
			return(isset($this->registeredWowDataHandlers[$wd]) && !empty($this->registeredWowDataHandlers[$wd]));
		}
		return(isset($this->registeredWowDataHandlers[$wd][$mode]) && !empty($this->registeredWowDataHandlers[$wd][$mode]));
	}
	
	/**
	 * Fetches the options of a registered WowData subclass and mode handler
	 * @param String $wd class name of the WowData subclass
	 * @param String $mode 'read' or 'write' for query mode
	 * @return array|false option array on success, false on failure
	 */
	protected function getWowDataHandlers($wd, $mode) {
		if($this->areWowDataHandlersRegistered($wd, $mode)) {
			return($this->registeredWowDataHandlers[$wd][$mode]);
		}
		return(false);
	}
	
	/**
	 * Recursive merging of two arrays with the secondary overwriting the primary
	 * @return array merged array
	 */
	protected function mergeOptions($primary, $secondary) {
		foreach($secondary as $key => $value)
		{
			if(array_key_exists($key, $primary) && is_array($value))
				$primary[$key] = $this->mergeOptions($primary[$key], $secondary[$key]);
			else
				$primary[$key] = $value;
		}
		return $primary;
	}
}

/**
 * Basic data channel for alle channels that will need to process xml
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
abstract class WowDataChannelXML extends WowDataChannel {
	/**
	 * Registered xml data
	 * @var array
	 */
	protected $xmlData = array();
	
	/**
	 * Registers a xml string under a specified id
	 * @param string $id id for this xml string
	 * @param string $xml xml content
	 */
	protected function registerXML($id, $xml) {
		$domdoc = new DOMDocument();
		$domdoc->loadXML($xml);
		$domxpath = new DOMXPath($domdoc);
		if($domxpath) {
			$this->xmlData[$id] = $domxpath;
			return(true);
		}
		return(false);
	}
	
	/**
	 * Performs one or more xpath queries to get data and subdata
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return mixed result of the xpath query or queries
	 */
	protected function getDataFromXPath($xmlid, $xpath, $root = null) {
		$c = null;
		if(is_array($xpath)) {
			if((count($xpath) == 2) && isset($xpath[0]) && isset($xpath[1])) {
				/**
				 * If the xpath is an indexed array with only two keys, we are supposed to
				 * treat key 1 as a xpath query and key 2 as a new xpath collection we should
				 * query relative to the node(s) found by the key 1 query
				 */
				$test = $this->testXPath($xmlid, $xpath[0], $root);
				if($test) {
					if($test > 1) {
						$c = array();
						$nodes = $this->getNodes($xmlid, $xpath[0], $root);
						foreach($nodes as $node) {
							$c[] = $this->getDataFromXPath($xmlid, $xpath[1], $node);
						}
					} else {
						$c = $this->getDataFromXPath($xmlid, $xpath[1], $root);
					}
				}
			} else {
				/**
				 * If we do not have an indexed array with two keys, we assume an
				 * associative array that should be parsed as key-value pairs of data ids
				 * and their xpaths
				 */
				$c = array();
				foreach($xpath as $subdataid => $subxpath) {
					$c[$subdataid] = $this->getDataFromXPath($xmlid, $subxpath, $root);
				}
			}
		} else {
			$test = $this->testXPath($xmlid, $xpath, $root);
			if($test) {
				if($test > 1) {
					$c = array();
					$values = $this->getNodes($xmlid, $xpath, $root);
					foreach($values as $value) {
						$c[] = $this->getString($xmlid, 'text()', $value);
					}
				} else {
					$c = $this->getString($xmlid, $xpath, $root);
				}
			}
		}
		return($c);
	}
	
	/**
	 * Performs a xpath query on a registered xml
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return DOMNode|DOMNodeList result of the xpath query
	 */
	protected function runXPath($id, $xpath, $root = null) {
		if(!isset($this->xmlData[$id]) || !is_a($this->xmlData[$id], 'DOMXPath')) {
			WowDataAccess::obj()->error('Data Error in "'.__CLASS__.'" - No DOMXPath found for id "'.$id.'"!', 500);
			return(false);
		}
		if(is_a($root, 'DOMNode')) {
			return($this->xmlData[$id]->evaluate($xpath, $root));
		} else {
			return($this->xmlData[$id]->evaluate($xpath));
		}
	}
	
	/**
	 * Verifies a xpath expression
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return integer|false number of found nodes on success, false if not
	 */
	protected function testXPath($id, $xpath, $root = null) {
		$result = $this->runXPath($id, ('count('.$xpath.')'), $root);
		if($result > 0) {
			return(intval($result));
		}
		return(false);
	}
	
	/**
	 * Enforced xpath query for a singular string value
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return string|null result of the xpath query, null if more than one result was found
	 */
	public function getString($id, $xpath, $root = null) {
		$result = $this->runXPath($id, ('string('.$xpath.')'), $root);
		if(!is_a($result, 'DOMNodeList')) {
			return(strval($result));
		}
		return(null);
	}
	
	/**
	 * Enforced xpath query for a singular integer value
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return integer|null result of the xpath query, null if more than one result was found
	 */
	public function getInteger($id, $xpath, $root = null) {
		$result = $this->runXPath($id, ('string('.$xpath.')'), $root);
		if(!is_a($result, 'DOMNodeList')) {
			return(intval($result));
		}
		return(null);
	}
	
	/**
	 * Enforced xpath query for a singular float value
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return float|null result of the xpath query, null if more than one result was found
	 */
	public function getFloat($id, $xpath, $root = null) {
		$result = $this->runXPath($id, ('string('.$xpath.')'), $root);
		if(!is_a($result, 'DOMNodeList')) {
			return(floatval($result));
		}
		return(null);
	}
	
	/**
	 * Enforced xpath query for a list of nodes
	 * @param string $id registered xml id
	 * @param string $xpath xpath expession
	 * @param DOMNode|null $root xml node to act as a relative root for the xpath expression
	 * @return DOMNodeList|null result of the xpath query, null if nothing was found
	 */
	public function getNodes($id, $xpath, $root = null) {
		$result = $this->runXPath($id, $xpath, $root);
		if(is_a($result, 'DOMNodeList')) {
			return($result);
		}
		return(null);
	}
}

?>