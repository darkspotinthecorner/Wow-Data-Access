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
 * _TemplateWowDataChannel.class.php, [Short description of file contents]
 * 
 * @author your name <your email>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// _TemplateWowDataChannel.class.php

/**
 * [Short description of the data channel functionality]
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
class _TemplateWowDataChannel extends WowDataChannel {
	/**
	 * Array that contains the options and may be filled with defaults
	 * @var array
	 */
	protected $options = array(
		/*
		 * You may define any number of key-value-pairs
		 */
		'example a' => array(
			'example a 1' => 'foo?',
			'example a 2' => 'bar.',
			'example a 3' => 'baz!',
		),
		'example b' => 'faz?!',
		/*
		 * The key 'schemes' is reserved for WowData subclass specific options
		 */
		'schemes' => array(
			'WowDataRealm' => array(
				'option a' => 100,
				'option b' => 200,
				'option c' => 300,
			),
			'WowDataIcon' => array(
				'option a' => 12345,
				'option x' => array(
					'icon option a 1' => 'aaa',
					'icon option a 2' => 'bbb',
					'icon option a 3' => 'ccc',
				),
				'option y' => 'foo? bar. baz!',
			),
		),
	);
	
	/**
	 * You may also define custom properties
	 */
	protected $firstprop  = true;
	protected $secondprop = false;
	protected $thirdprop  = 3.14;
	
	/**
	 * The initChecksCore() method must be present
	 *
	 * Inside this method you should check your $this->options array for any needed
	 * parameters and validate them.
	 *
	 * If anything goes wrong, you should report the error to the module by calling:
	 * WowDataAccess::obj()->error('Your error message', 500); return(false);
	 *
	 * Notice that this method must return true, else the channel will not be loaded
	 * by the WowDataAccess module.
	 */
	protected function initChecksCore() {
		/**
		 * Checks an option
		 */
		if(!isset($this->options['example a']) || empty($this->options['example a'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (example a) is missing or empty!', 500);
			return(false);
		}
		
		return(true);
	}
	
	/**
	 * The initChecksWowDataScheme($wd, $options) method must be present
	 *
	 * Inside this method you should check a specific $this->options['schemes']
	 * element for needed options or parameters and validate them.
	 *
	 * If anything goes wrong, you should report the error to the module by calling:
	 * WowDataAccess::obj()->error('Your error message', 500); return(false);
	 *
	 * Notice that this method must return true, else the WowData scheme will not
	 * be active in this data channel.
	 */
	protected function initChecksWowDataScheme($wd, $options = array()) {
		/**
		 * Checks an option
		 */
		if(!isset($options['option a']) || empty($options['option a'])) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/option a) is missing or empty!', 500);
			return(false);
		}
		
		return(true);
	}
	
	/**
	 * The initRegisterWowDataHandlers() method must be present
	 *
	 * Inside this method you need to register WowData handlers. This means that
	 * you will set what methods should be used to handle incoming parameters
	 */
	protected function initRegisterWowDataHandlers() {
		/**
		 * Register read and write handlers for WowDataRealm
		 *
		 * The registerWowDataHandler method accepts three paramters:
		 * 1st Param: class name of the WowData subclass
		 * 2nd Param: context, must be 'read' or 'write'
		 * 3rd Param: options:
		 *   'handler': The name of the method that will be used to handle the
		 *       parameters
		 *   'multiSupport': Does the method (3rd param) support and expects the
		 *       passing of more parameters
		 *
		 * Note that any WowData subclasses without a registered handler will not be
		 * activated by the channel.
		 */
		$this->registerWowDataHandler('WowDataRealm', 'read',  array('multiSupport' => true,  'handler' => 'openRealm'));
		$this->registerWowDataHandler('WowDataRealm', 'write', array('multiSupport' => true,  'handler' => 'saveRealm'));
	}
	
	/**
	 * This is the handler method that tries to open one or more realms
	 * 
	 * A method that is bound to a 'read' handler will always receive 2 params:
	 * 1st Param: class name of the WowData subclass
	 * 2nd Param: parameter array that contains the params needed to query one or
	 *     more realms.
	 */
	public function openRealm($wd, $params) {
		/*
		 * This should be used as it ensures that deactivated WowData schemes are not
		 * processed
		 */
		if($this->isWowDataClassRegistered($wd)) {
			/*
			 * Loads the scheme options into $opt
			 */
			$opt = $this->getWowDataClassOptions($wd);
			
			/*
			 * If your method is registered with 'multiSupport' you need to treat the
			 * $params as an collection array that contains grouped parameter arrays
			 */
			foreach($params as $pmapid => $subparams) {
				/*
				 * Process the params
				 */
			}
			
			/*
			 * Query your resource
			 */
			
			/*
			 * Prepare your result for return
			 *
			 * IMPORTANT: You need to return a WowData subclass object or an array of
			 * these objects
			 *
			 * If your method is registered with 'multiSupport' you need to return a
			 * collection array that contains your objects
			 */
			 return($result);
		}
		return(false);
	}
	
	/**
	 * This is the handler method that tries to save one or more realms
	 * 
	 * A method that is bound to a 'write' handler will always receive 2 params:
	 * 1st Param: class name of the WowData subclass
	 * 2nd Param: parameter array that contains the params needed to query one or
	 *     more realms.
	 */
	protected function save($wd, $wdo) {
		/*
		 * This should be used as it ensures that deactivated WowData schemes are not
		 * processed
		 */
		if($this->isWowDataClassRegistered($wd)) {
			/*
			 * Loads the scheme options into $opt
			 */
			$opt = $this->getWowDataClassOptions($wd);
			
			/*
			 * If your method is registered with 'multiSupport' you need to treat the
			 * $params as an collection array that contains grouped parameter arrays
			 */
			foreach($wdo as $w) {
				/*
				 * Check for valid objects only
				 */
				if(is_subclass_of($w, 'WowData') && $w->isValid()) {
					/*
					 * Process the objects
					 */
				}
			}
			
			/*
			 * Save to your resource
			 */
			
			/*
			 * Prepare your result for return
			 *
			 * IMPORTANT: You need to return a boolean true/false or an array of booleans
			 *
			 * If your method is registered with 'multiSupport' you need to return a
			 * collection array that contains your booleans
			 */
			 return($result);
		}
		return(false);
	}
}

?>