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
 * WowDataChannelBattleNet.class.php, Data channel definition for xml access via
 * the battle.net website
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataChannelBattleNet.class.php

/**
 * Data channel definition for xml access via battle.net
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
class WowDataChannelBattleNet extends WowDataChannel {
	/**
	 * Array that contains the options and may be filled with defaults
	 * @var array
	 */
	protected $options = array(
		'api' => array(
			'auth' => array(
				'directive'  => 'BNET',
				'publickey'  => 'your_public_key',
				'privatekey' => 'your_private_key',
			),
		),
		'remote' => array(
			'basePath' => array(
				WowDataAccess::REGION_US => 'http://us.battle.net/api/wow/',
				WowDataAccess::REGION_EU => 'http://eu.battle.net/api/wow/',
				WowDataAccess::REGION_KR => 'http://kr.battle.net/api/wow/',
				WowDataAccess::REGION_TW => 'http://tw.battle.net/api/wow/',
				WowDataAccess::REGION_CN => 'http://battlenet.com.cn/api/wow',
			),
			'curl' => array(
				'timeout'        => 2,
				'timeoutConnect' => 4,
			),
			'timestampFormat' => DATE_RFC2822, // 'D, j M Y H:i:s e',
		),
		'schemes' => array(
			'WowDataRealm' => array(
				'extPath' => 'realm/status',
				'params'  => array(
					'region' => array('optional' => false),
					'realm'  => array(
						'optional' => true,
						'concat'   => ',',
					),
				),
			),
			'WowDataCharacter' => array(
				'extPath' => 'character/{realm}/{character}',
				'params'  => array(
					'locale'    => array('optional' => false),
					'region'    => array('optional' => false),
					'realm'     => array('optional' => false),
					'character' => array('optional' => false),
					'fields'    => array(
						'optional' => true,
						'concat'   => ',',
						'default'  => array('stats', 'talents', 'items'),
						'valid'    => array('guild', 'stats', 'talents', 'items', 'reputation', 'titles', 'professions', 'appearance', 'companions', 'mounts', 'pets', 'achievements', 'progression'),
					),
				),
				
			),
			'WowDataCharacterThumbnail' => array(
				'basePath' => array(
					WowDataAccess::REGION_US => 'http://us.battle.net/static-render/{region}/{filename}',
					WowDataAccess::REGION_EU => 'http://eu.battle.net/static-render/{region}/{filename}',
					WowDataAccess::REGION_KR => 'http://kr.battle.net/static-render/{region}/{filename}',
					WowDataAccess::REGION_TW => 'http://tw.battle.net/static-render/{region}/{filename}',
					WowDataAccess::REGION_CN => 'http://battlenet.com.cn/static-render/{region}/{filename}',
				),
				'params'  => array(
					'region'   => array('optional' => false),
					'filename' => array('optional' => false),
				),
				
			),
			'WowDataGuild' => array(
				'extPath' => 'guild/{realm}/{guild}',
				'params'  => array(
					'locale' => array('optional' => false),
					'region' => array('optional' => false),
					'realm'  => array('optional' => false),
					'guild'  => array('optional' => false),
					'fields' => array(
						'optional' => true,
						'concat'   => ',',
						'default'  => array('members'),
						'valid'    => array('members', 'achievements'),
					),
				),
				
			),
			'WowDataItem' => array(
				'extPath' => 'item/{itemid}',
				'params'   => array(
					'locale' => array('optional' => false),
					'region' => array('optional' => false),
					'itemid' => array('optional' => false),
				),
			),
			'WowDataIcon' => array(
				'basePath' => array(
					WowDataAccess::REGION_US => 'http://us.battle.net/wow-assets/static/images/icons/56/{icon}.jpg',
					WowDataAccess::REGION_EU => 'http://eu.battle.net/wow-assets/static/images/icons/56/{icon}.jpg',
					WowDataAccess::REGION_KR => 'http://kr.battle.net/wow-assets/static/images/icons/56/{icon}.jpg',
					WowDataAccess::REGION_TW => 'http://tw.battle.net/wow-assets/static/images/icons/56/{icon}.jpg',
					WowDataAccess::REGION_CN => 'http://battlenet.com.cn/wow-assets/static/images/icons/56/{icon}.jpg',
				),
				'params'   => array(
					'region' => array('optional' => false),
					'icon'   => array('optional' => false),
				),
			),
		),
	);
	
	/**
	 * Tries to find an item specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'locale', 'region' and 'itemid' are used
	 * @return WowDataItem|false WowDataItem on success or false if nothing was found
	 */
	public function openRealm($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url = $this->buildUrl($wd, $params);
			WowDataAccess::obj()->log(__METHOD__.' >>> API Connect: <a href="'.$url.'">'.$url.'</a>');
			$data = json_decode($this->getRemoteData($url), true);
			if(!empty($data['realms']) && (count($data['realms']) == 1)) {
				if(!empty($data['realms'][0])) {
					$r = $data['realms'][0];
					$realm = new WowDataRealm(array(
						'region'     => $params['region'],
						'name'       => $r['name'],
						'slug'       => $r['slug'],
						'type'       => $r['type'],
						'population' => $r['population'],
						'queue'      => $r['queue'],
						'status'     => $r['status'],
					));
					if($realm->isValid()) {
						return($realm);
					}
				}
			}
		}
		return(false);
	}
	
	/**
	 * Tries to find a character specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'locale', 'region' 'realm' and 'character' are used
	 * @return WowDataCharacter|false WowDataCharacter on success or false if nothing was found
	 */
	public function openCharacter($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url = $this->buildUrl($wd, $params);
			WowDataAccess::obj()->log(__METHOD__.' >>> API Connect: <a href="'.$url.'">'.$url.'</a>');
			$data = json_decode($this->getRemoteData($url), true);
			if(!empty($data)) {
				$character = new WowDataCharacter(array(
					'locale'            => $params['locale'],
					'region'            => $params['region'],
					'realm'             => $params['realm'],
					'name'              => $data['name'],
					'classid'           => $data['class'],
					'raceid'            => $data['race'],
					'genderid'          => $data['gender'],
					'level'             => $data['level'],
					'achievementPoints' => $data['achievementPoints'],
					'thumbnail'         => $data['thumbnail'],
				));
				// WowDataAccess::obj()->log(__METHOD__.' >>> WDA from API data: <br />'.print_r($character, true));
				if($character->isValid()) {
					return($character);
				}
			}
			WowDataAccess::obj()->log(__METHOD__.' >>> API Result: <br />'.print_r($data, true));
		}
		return(false);
	}
	
	/**
	 * Tries to find an icon specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'region' and 'filename' are used
	 * @return WowDataCharacterThumbnail|false WowDataCharacterThumbnail on success or false if nothing was found
	 */
	public function openCharacterThumbnail($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url       = $this->buildUrl($wd, $params);
			$data      = $this->getRemoteData($url);
			$thumbnail = new WowDataCharacterThumbnail();
			$thumbnail->set('region',   $params['region']);
			$thumbnail->set('filename', $params['filename']);
			$thumbnail->set('image',    $data);
			WowDataAccess::obj()->log(__METHOD__.' >>> Character Thumbnail fetch: <a href="'.$url.'">'.$url.'</a>');
			return($thumbnail);
		}
		return(false);
	}
	
	/**
	 * Tries to find a guild specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'locale', 'region' 'realm' and 'guild' are used
	 * @return WowDataGuild|false WowDataGuild on success or false if nothing was found
	 */
	public function openGuild($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url = $this->buildUrl($wd, $params);
			WowDataAccess::obj()->log(__METHOD__.' >>> API Connect: <a href="'.$url.'">'.$url.'</a>');
		}
		return(false);
	}
	
	/**
	 * Tries to find an item specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'locale', 'region' and 'itemid' are used
	 * @return WowDataItem|false WowDataItem on success or false if nothing was found
	 */
	public function openItem($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url = $this->buildUrl($wd, $params);
			WowDataAccess::obj()->log(__METHOD__.' >>> API Connect: <a href="'.$url.'">'.$url.'</a>');
			$data = json_decode($this->getRemoteData($url), true);
			if(!empty($data)) {
				$item = new WowDataItem();
				
				WowDataAccess::obj()->log(__METHOD__.' >>> WDA from API data: <br />'.print_r($item, true));
				
				if($item->isValid()) {
					return($item);
				}
			} else {
				WowDataAccess::obj()->log(__METHOD__.' >>> $data is empty?!!');
			}
			WowDataAccess::obj()->log(__METHOD__.' >>> API Result: <br />'.print_r($data, true));
		}
		return(false);
	}
	
	/**
	 * Tries to find an icon specified by the data in $params on the battle.net
	 * website
	 * @param array $params the keys 'region' and 'icon' are used
	 * @return WowDataIcon|false WowDataIcon on success or false if nothing was found
	 */
	public function openIcon($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$url  = $this->buildUrl($wd, $params);
			$data = $this->getRemoteData($url);
			$icon = new WowDataIcon();
			$icon->set('region', $params['region']);
			$icon->set('icon',   $params['icon']);
			$icon->set('image',  $data);
			WowDataAccess::obj()->log(__METHOD__.' >>> Icon fetch: <a href="'.$url.'">'.$url.'</a>');
			return($icon);
		}
		return(false);
	}
	
	/**
	 * Registers the WowData subclass handlers
	 */
	protected function initRegisterWowDataHandlers() {
		$this->registerWowDataHandler('WowDataRealm',              'read',  array('multiSupport' => false, 'handler' => 'openRealm'));
		$this->registerWowDataHandler('WowDataCharacter',          'read',  array('multiSupport' => false, 'handler' => 'openCharacter'));
		$this->registerWowDataHandler('WowDataCharacterThumbnail', 'read',  array('multiSupport' => false, 'handler' => 'openCharacterThumbnail'));
		$this->registerWowDataHandler('WowDataGuild',              'read',  array('multiSupport' => false, 'handler' => 'openGuild'));
		$this->registerWowDataHandler('WowDataItem',               'read',  array('multiSupport' => false, 'handler' => 'openItem'));
		$this->registerWowDataHandler('WowDataIcon',               'read',  array('multiSupport' => false, 'handler' => 'openIcon'));
	}
	
	/**
	 * Perform basic checks on the channel options iteself
	 * @return boolean true success, false on failure
	 */
	protected function initChecksCore() {
		/**
		 * Nothing to do in this channel
		 */
		return(true);
	}
	
	/**
	 * Perform checks on the WowData subclass options
	 * @return boolean true success, false on failure
	 */
	protected function initChecksWowDataScheme($wd, $options = array()) {
		/**
		 * Nothing to do in this channel
		 */
		return(true);
	}
	
	/**
	 * Queries the remote battle.net with a given url
	 * @param string $url url to query
	 * @return string|false content on success, false on failure
	 */
	protected function getRemoteData($url) {
		$content = false;
		/**
		 * Check if libcurl is compiled for this php installation
		 */
		if(function_exists('curl_init')) {
			$curl = curl_init($url);
			
			$httpheaders = array();
			
			/*
			 * Auth
			 */
			if(!empty($this->options['api']['auth'])) {
				
				$apipath    = preg_replace('/^.+\/api\/wow/iu', '/api/wow', $url);
				$timestring = $this->getGMTString();
				$signature  = $this->getRequestSignature($timestring, $apipath);
				
				$httpheaders[] = ('Date: '.$timestring);
				$httpheaders[] = ('Authorization: '.$signature);
			}
			
			WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r($httpheaders, true));
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheaders);
			
			if(isset($this->options['remote']['curl']['timeout'])) {
				curl_setopt($curl, CURLOPT_TIMEOUT, $this->options['remote']['curl']['timeout']);
			}
			if(isset($this->options['remote']['curl']['timeoutConnect'])) {
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->options['remote']['curl']['timeoutConnect']);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($curl);
			
			curl_close($curl);
		} else {
			$content = file_get_contents($url);
		}
		return($content);
	}
	
	/**
	 * Fetches the basePath depending on the region
	 * @param array basePath array indexed by region keys
	 * @param array param array, should contain the 'region' key
	 * @return string|false basePath on success, false on failure
	 */
	protected function getBasePath($pathmap, $params) {
		if(is_array($pathmap)) {
			if(!empty($params['region'])) {
				if(!empty($pathmap[$params['region']])) {
					return($pathmap[$params['region']]);
				}
			}
			return(array_shift($pathmap));
		} else {
			if(!empty($pathmap)) {
				return($pathmap);
			}
		}
		return(false);
	}
	
	/**
	 * Builds a queryable url from the options with the given parameters
	 * @param string $wd WowData subclass (scheme) to build for
	 * @param string $xmlid xml file identifier to allow a scheme to include more than one queried xml
	 * @param string $params associative array that contains the parameters that will be parsed into the url
	 * @return string|false url on success, false on failure
	 */
	protected function buildUrl($wd, array $params) {
		// WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r(array('$wd' => $wd, '$params' => $params), true));
		if($this->isWowDataClassRegistered($wd)) {
			$opt = $this->getWowDataClassOptions($wd);
			/*
			 * Prepare the basePath
			 * Look up the scheme's options first, then fall back to the generic basePath
			 */
			$opt_basepath = false;
			if(isset($opt['basePath'])) {
				$opt_basepath = $this->getBasePath($opt['basePath'], $params);
			}
			if($opt_basepath) {
				$url = $opt_basepath;
			} else {
				if(!empty($opt['extPath'])) {
					$url = $this->getBasePath($this->options['remote']['basePath'], $params) . $opt['extPath'];
				} else {
					$url = $this->getBasePath($this->options['remote']['basePath'], $params);
				}
			}
			$c = array();
			foreach($opt['params'] as $pkey => $popts) {
				if($popts['optional'] === false) {
					if(!isset($params[$pkey])) {
						WowDataAccess::obj()->error('Build url error in "'.__CLASS__.'" - Mandatory url parameter "'.$pkey.'" is missing!', 500);
						return(false);
					}
					$url = str_replace(('{'.$pkey.'}'), $this->prepareUrlParameter($pkey, $params[$pkey]), $url);
				} else {
					// WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r(array('$pkey' => $pkey, '$params[$pkey]' => $params[$pkey]), true));
					if(!empty($params[$pkey])) {
						$c[$pkey] = $params[$pkey];
					} else {
						if(!empty($popts['default'])) {
							$c[$pkey] = $popts['default'];
						}
					}
				}
			}
			if(!empty($c)) {
				$spliturl = explode('?', $url);
				if(count($spliturl) > 1) {
					$url   .= '&';
					$concat = '&';
				} else {
					$concat = '?';
				}
				foreach($c as $key => $value) {
					if(is_array($value) && !isset($opt['params'][$key]['concat'])) {
						foreach($value as $subvalue) {
							$url .= $concat . $key . '=' .  $this->prepareUrlParameter($key, $subvalue);
							$concat = '&';
						}
					} else {
						$url .= $concat . $key . '=' . $this->prepareUrlParameter($key, $value);
						$concat = '&';
					}
				}
			}
			return($url);
		}
		return(false);
	}
	
	/**
	 * Builds a GMT time string of the current timestamp
	 * @return string the time as string
	 */
	protected function getGMTString($time = null) {
		if($time === null) {
			$time = time();
		}
		return(date($this->options['remote']['timestampFormat'], $time));
	}
	
	/**
	 * Generates a request signature to send with an authorized api call
	 * @param string $timestring the timestamp string sent with the request
	 * @param string $apipath the api subpath this request goes to (/api/wow...)
	 * @return string the signature string
	 */
	protected function getRequestSignature($timestring, $apipath) {
		if(!empty($this->options['api']['auth'])) {
			if(!empty($this->options['api']['auth']['directive']) && !empty($this->options['api']['auth']['publickey']) && !empty($this->options['api']['auth']['privatekey'])) {
				$directive  = $this->options['api']['auth']['directive'];
				$publickey  = $this->options['api']['auth']['publickey'];
				$privatekey = $this->options['api']['auth']['privatekey'];
				$signme     = 'GET' . "\n" . $timestring . "\n" . $apipath . "\n";
				$hashed     = base64_encode(hash_hmac('sha1', $signme, $privatekey, true));
				$signature  = $directive . ' ' . $publickey . ':' . $hashed;
				return($signature);
			}
		}
		return(false);
	}
	
	/**
	 * Performs a context sensitive cleaning and pre-processing of url parameters
	 * @param string $key url parameter key
	 * @param string $value url parameter value
	 * @return mixed processed parameter value
	 */
	protected function prepareUrlParameter($key, $value) {
		// WowDataAccess::obj()->log(__METHOD__.' >>> '.print_r(array('key' => $key, 'value' => $value), true));
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
				$work = preg_replace('/[^-a-zA-Z ]/', '', $work);
				$work = preg_replace('/ /', "-", $work);
				break;
			case 'character':
				$work = strtolower(trim($work));
				break;
			case 'fields':
				$work = implode(',', $work);
				break;
			case 'slotid':
			case 'itemid':
				$work = intval($work);
				break;
			case 'icon':
				$work = strtolower(trim($work));
				break;
		}
		// $work = urlencode($work);
		return($work);
	}
}

?>