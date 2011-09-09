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
 * WowDataChannelMySQL.class.php, Data channel definition for basic mysql access
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataChannelMySQL.class.php

/**
 * Data channel definition for basic MySQL access
 * @package wow-data-access
 * @subpackage wow-data-channel
 */
class WowDataChannelMySQL extends WowDataChannel {
	/**
	 * Array that contains the options and may be filled with defaults
	 * @var array
	 */
	protected $options = array(
		'mysql' => array(
			'server'   => 'MySQLServerAdress',
			'user'     => 'MySQLServerUser',
			'password' => 'MySQLServerPassword',
			'database' => 'MySQLServerDatabase',
		),
		'schemes' => array(
			'WowDataRealm' => array(
				'tablename' => 'wow_realm',
				'storage'   => 'wdo',
				'fields'    => array(
					'region'     => array('dataid' => 'region'    , 'type' => 'VARCHAR(20)' , 'openkey' => 'region'),
					'slug'       => array('dataid' => 'slug'      , 'type' => 'VARCHAR(100)', 'openkey' => 'realm' ),
					'name'       => array('dataid' => 'name'      , 'type' => 'VARCHAR(100)'),
					'type'       => array('dataid' => 'type'      , 'type' => 'VARCHAR(20)' ),
					'population' => array('dataid' => 'population', 'type' => 'VARCHAR(20)' ),
					'queue'      => array('dataid' => 'queue'     , 'type' => 'TINYINT(1)'  ),
					'status'     => array('dataid' => 'status'    , 'type' => 'TINYINT(1)'  ),
				),
				'lifetime' => array(
					'field' => 'timestamp',
					'time'  => 300, // 5 minutes
				),
			),
			'WowDataItem' => array(
				'tablename' => 'wow_item',
				'storage'   => 'wdo',
				'fields'    => array(
					'locale'     => array('dataid' => 'locale'          , 'type' => 'VARCHAR(20)' , 'openkey' => 'locale'),
					'region'     => array('dataid' => 'region'          , 'type' => 'VARCHAR(20)' , 'openkey' => 'region'),
					'id'         => array('dataid' => 'id'              , 'type' => 'INT(10)'     , 'openkey' => 'itemid'),
					'name'       => array('dataid' => 'name'            , 'type' => 'VARCHAR(200)'),
					'icon'       => array('dataid' => 'icon'            , 'type' => 'VARCHAR(200)'),
					'qid'        => array('dataid' => 'overallQualityId', 'type' => 'INT(4)'      ),
					'cid'        => array('dataid' => 'classId'         , 'type' => 'INT(4)'      ),
					'bonding'    => array('dataid' => 'bonding'         , 'type' => 'INT(4)'      ),
					'rlvl'       => array('dataid' => 'requiredLevel'   , 'type' => 'INT(10)'     ),
					'ilvl'       => array('dataid' => 'itemLevel'       , 'type' => 'INT(10)'     ),
				),
				'lifetime' => array(
					'field' => 'timestamp',
					'time'  => 2592000, // 30 days
				),
			),
			'WowDataItemEquipped' => array(
				'tablename' => 'wow_item_equipped',
				'storage'   => 'wdo',
				'fields'    => array(
					'locale'     => array('dataid' => 'locale'          , 'type' => 'VARCHAR(20)' , 'openkey' => 'locale'   ),
					'region'     => array('dataid' => 'region'          , 'type' => 'VARCHAR(20)' , 'openkey' => 'region'   ),
					'realm'      => array('dataid' => 'realm'           , 'type' => 'VARCHAR(100)', 'openkey' => 'realm'    ),
					'character'  => array('dataid' => 'character'       , 'type' => 'VARCHAR(50)' , 'openkey' => 'character'),
					'slot'       => array('dataid' => 'slotId'          , 'type' => 'INT(4)'      , 'openkey' => 'slotid'   ),
					'id'         => array('dataid' => 'id'              , 'type' => 'INT(10)'     , 'openkey' => 'itemid'   ),
					'name'       => array('dataid' => 'name'            , 'type' => 'VARCHAR(200)'),
					'icon'       => array('dataid' => 'icon'            , 'type' => 'VARCHAR(200)'),
					'qid'        => array('dataid' => 'overallQualityId', 'type' => 'INT(4)'      ),
					'cid'        => array('dataid' => 'classId'         , 'type' => 'INT(4)'      ),
					'bonding'    => array('dataid' => 'bonding'         , 'type' => 'INT(4)'      ),
					'rlvl'       => array('dataid' => 'requiredLevel'   , 'type' => 'INT(10)'     ),
					'ilvl'       => array('dataid' => 'itemLevel'       , 'type' => 'INT(10)'     ),
				),
				'lifetime' => array(
					'field' => 'timestamp',
					'time'  => 259200, // 3 days
				),
			),
			'WowDataCharacter' => array(
				'tablename' => 'wow_character',
				'storage'   => 'wdo',
				'fields'    => array(
					'locale'     => array('dataid' => 'locale', 'type' => 'VARCHAR(20)' , 'openkey' => 'locale'   ),
					'region'     => array('dataid' => 'region', 'type' => 'VARCHAR(20)' , 'openkey' => 'region'   ),
					'realm'      => array('dataid' => 'realm' , 'type' => 'VARCHAR(100)', 'openkey' => 'realm'    ),
					'name'       => array('dataid' => 'name'  , 'type' => 'VARCHAR(50)' , 'openkey' => 'character'),
				),
				'lifetime' => array(
					'field' => 'timestamp',
					'time'  => 86400, // 1 day
				),
			),
			'WowDataCharacterThumbnail' => array(
				'tablename' => 'wow_character_thumbnail',
				'storage'   => 'wdo',
				'fields'    => array(
					'region'   => array('dataid' => 'region',    'type' => 'VARCHAR(20)' , 'openkey' => 'region'  ),
					'filename' => array('dataid' => 'filename' , 'type' => 'VARCHAR(200)', 'openkey' => 'filename'),
					'image'    => array('dataid' => 'image',     'type' => 'BLOB'        ),
				),
				'lifetime' => array(
					'field' => 'timestamp',
					'time'  => 2592000, // 30 days
				),
			),
			'WowDataGuild' => false,
			'WowDataIcon' => array(
				'tablename' => 'wow_icon',
				'storage'   => 'wdo',
				'fields'    => array(
					'region' => array('dataid' => 'region', 'type' => 'VARCHAR(20)' , 'openkey' => 'region'),
					'icon'   => array('dataid' => 'icon' ,  'type' => 'VARCHAR(200)', 'openkey' => 'icon'),
					'image'  => array('dataid' => 'image',  'type' => 'BLOB'        ),
				),
			),
		),
	);
	
	/**
	 * This will hold the MySQL connection resource returned by mysql_connect()
	 * @var boolean|resource
	 */
	protected $dblink = false;
	
	/**
	 * Tries to find a data object specified by the method in $method and data in $params
	 * @param string $method the method that will identify the WowData subclass used
	 * @param array $params access parameters like 'locale', 'region' or 'realm'
	 * @return mixed Appropriate WowData subclass on success or false if nothing was found
	 */
	public function open($wd, $params) {
		if($this->isWowDataClassRegistered($wd)) {
			$opt   = $this->getWowDataClassOptions($wd);
			$where = array();
			$order = array();
			$limit = 1;
			foreach($params as $pmapid => $subparams) {
				$c = array();
				foreach($opt['fields'] as $mysqlfield => $info) {
					if(!empty($info['openkey'])) {
						$c[$mysqlfield] = array('compare' => '=', 'value' => $subparams[$info['openkey']]);
					}
				}
				if(!empty($opt['lifetime'])) {
					if(intval($opt['lifetime']['time']) >= 0) {
						$c[$opt['lifetime']['field']] = array('compare' => '>', 'value' => (time() - intval($opt['lifetime']['time'])));
						$order[$opt['lifetime']['field']] = 'DESC';
					}
				}
				$where[$pmapid] = $c;
			}
			$limit = count($params);
			$res = $this->runOpenSQL(array(
				'table' => $opt['tablename'],
				'where' => $where,
				'order' => $order,
				'limit' => $limit,
			));
			if($res !== false) {
				$c = array();
				foreach($res as $r) {
					$wdo = unserialize($r[$opt['storage']]);
					if(($wdo !== false) && (get_class($wdo) === $wd) && ($wdo->isValid())) {
						$c[] = $wdo;
					}
				}
				return($c);
			}
		}
		return(false);
	}
	
	/**
	 * Saves a WowData Subclass object into the mysql database
	 * @param mixed $wdo WowData subclass object to save
	 * @return boolean true on success, false on failure
	 */
	protected function save($wd, $wdo) {
		if($this->isWowDataClassRegistered($wd)) {
			$opt = $this->getWowDataClassOptions($wd);
			$set = array();
			foreach($wdo as $w) {
				if(is_subclass_of($w, 'WowData') && $w->isValid()) {
					$c = array();
					foreach($opt['fields'] as $mysqlfield => $info) {
						$c[$mysqlfield] = strval($w->get($info['dataid']));
					}
					if(!empty($opt['lifetime'])) {
						$c[$opt['lifetime']['field']] = time();
					}
					$c[$opt['storage']] = serialize($w);
					$set[] = $c;
				}
			}
			if(!empty($set)) {
				$res = $this->runSaveSQL(array(
					'table' => $opt['tablename'],
					'set'   => $set,
				));
				$c = array();
				foreach($wdo as $w) {
					$c[] = $res;
				}
				return($c);
			}
		}
		return(false);
	}
	
	/**
	 * Registers the WowData subclass handlers
	 */
	protected function initRegisterWowDataHandlers() {
		$this->registerWowDataHandler('WowDataRealm',              'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataRealm',              'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataItem',               'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataItem',               'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataItemEquipped',       'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataItemEquipped',       'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataCharacter',          'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataCharacter',          'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataCharacterThumbnail', 'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataCharacterThumbnail', 'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataGuild',              'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataGuild',              'write', array('multiSupport' => true, 'handler' => 'save'));
		$this->registerWowDataHandler('WowDataIcon',               'read',  array('multiSupport' => true, 'handler' => 'open'));
		$this->registerWowDataHandler('WowDataIcon',               'write', array('multiSupport' => true, 'handler' => 'save'));
	}
	
	/**
	 * Perform basic checks on the channel options iteself
	 * @return boolean true success, false on failure
	 */
	protected function initChecksCore() {
		/**
		 * Checks for core options that are needed to connect to the mysql database
		 */
		if(empty($this->options['mysql']['server'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (mysql/server) is missing!', 500);
			return(false);
		}
		if(empty($this->options['mysql']['user'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (mysql/user) is missing!', 500);
			return(false);
		}
		if(!isset($this->options['mysql']['password'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (mysql/password) is missing!', 500);
			return(false);
		}
		if(empty($this->options['mysql']['database'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (mysql/database) is missing!', 500);
			return(false);
		}
		
		/**
		 * Checks if any schemes are set at all, without defined schemes the channel
		 * is useless
		 */
		if(empty($this->options['schemes'])) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Mandatory option (schemes) is missing!', 500);
			return(false);
		}
		
		/**
		 * Checks if the mysql database connection works
		 */
		$this->dblink = mysql_connect($this->options['mysql']['server'], $this->options['mysql']['user'], $this->options['mysql']['password']);
		if($this->dblink === false) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Could not establish a mysql connection with the given parameters!', 500);
			return(false);
		}
		
		/**
		 * Checks if the database can be selected
		 */
		if(!mysql_select_db($this->options['mysql']['database'], $this->dblink)) {
			WowDataAccess::obj()->error(__METHOD__.' >>> Could not select the database ('.$this->options['mysql']['database'].')!', 500);
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
		 * Check if the scheme has a tablename defined
		 */
		if(empty($options['tablename'])) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/tablename) is missing!', 500);
			return(false);
		}
		
		/**
		 * Check if the given tablename exists in the mysql database
		 */
		$mysql_fields = mysql_query('SHOW COLUMNS FROM `'.mysql_real_escape_string($options['tablename'], $this->dblink).'`');
		if($mysql_fields === false) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>>  MySQL table ('.$options['tablename'].') is missing!', 500);
			return(false);
		} else {
			/**
			 * Loads all column names from the mysql database
			 */
			$c = array();
			if(mysql_num_rows($mysql_fields) > 0) {
				while($row = mysql_fetch_assoc($mysql_fields)) {
					$c[$row['Field']] = $row['Type'];
				}
			} else {
				WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>>  MySQL table ('.$options['tablename'].') has no fields defined! This should not be possible, what the hell did you do?!', 500);
				return(false);
			}
			$mysql_fields = $c;
		}
		
		/**
		 * Check if there are any fields defined in the scheme
		 */
		if(empty($options['fields']) || !is_array($options['fields'])) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>>  Mandatory option (schemes/'.$wd.'/fields) is missing!', 500);
			return(false);
		}
		/**
		 * Check if all fields in the scheme are present in the mysql table
		 */
		$dataids = array();
		$dif = array();
		foreach($options['fields'] as $fieldname => $info) {
			// Check if the option fields exist
			if(empty($mysql_fields[$fieldname])) {
				$dif[] = $fieldname;
			} else {
				$dataids[] = $info['dataid'];
			}
		}
		if(!empty($dif)) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>>  MySQL fields in table ('.$options['tablename'].') are missing: '.implode(', ', $dif).'!', 500);
			return(false);
		}
		
		/**
		 * Check if all data ids defined in the scheme exist in the wow data subclass
		 */
		$wdo = new $wd();
		$c = array();
		foreach($options['fields'] as $fieldname => $info) {
			$c[$fieldname] = $info['dataid'];
		}
		$dif = $wdo->compareForMissingDefinitions($c);
		if(!empty($dif)) {
			WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>>  Option defined fields are unknown to the data container ('.implode(', ', $dif).')!', 500);
			return(false);
		}
		unset($wdo);
		
		/**
		 * Check if the needed storage field is set and present in the db
		 */
		if(isset($options['storage'])) {
			if(!isset($mysql_fields[$options['storage']])) {
				WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> MySQL field to store that data container is missing ('.$options['storage'].')!', 500);
				return(false);
			}
		} else {
			WowDataAccess::obj()->error('Register data container ('.$wd.' @ '.__CLASS__.') / Mandatory option (schemes/'.$wd.'/storage) is missing!', 500);
			return(false);
		}
		
		/**
		 * Check if a lifetime for the data in this channel is set and verify the
		 * options if so
		 */
		if(isset($options['lifetime']) && ($options['lifetime'] !== false)) {
			if(empty($options['lifetime']['field'])) {
				WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/lifetime/field) is missing!', 500);
				return(false);
			} else {
				if(empty($mysql_fields[$options['lifetime']['field']])) {
					WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> MySQL field is missing ('.$options['lifetime']['field'].')!', 500);
					return(false);
				}
				if(!isset($options['lifetime']['time'])) {
					WowDataAccess::obj()->error(__METHOD__.'('.$wd.') >>> Mandatory option (schemes/'.$wd.'/lifetime/time) is missing!', 500);
					return(false);
				}
			}
		}
		
		return(true);
	}
	
	/**
	 * Builds a SELECT query and runs it based on the passed parameters
	 * @param array $params associative array containing the information needed to build the MySQL statement
	 * @return array|false associative array containing the data from the channel on success, false on failure
	 */
	protected function runOpenSQL(array $params = array()) {
		$sql = 'SELECT * FROM `'.mysql_real_escape_string($params['table'], $this->dblink).'`';
		if(!empty($params['where'])) {
			$c = array();
			foreach($params['where'] as $pmapid => $subparams) {
				$subc = array();
				foreach($subparams as $key => $value) {
					$subc[] = ' `'.mysql_real_escape_string($key, $this->dblink).'` '.$value['compare'].' \''.mysql_real_escape_string($value['value'], $this->dblink).'\'';
				}
				$c[] = '('.implode(' AND ', $subc).')';
			}
			if(!empty($c)) {
				$sql .= ' WHERE '.implode(' OR ', $c);
			}
		}
		if(!empty($params['order'])) {
			$c = array();
			foreach($params['order'] as $col => $dir) {
				$c[] = ' `'.mysql_real_escape_string($col, $this->dblink).'` '.($dir == 'DESC' ? 'DESC' : 'ASC');
			}
			if(!empty($c)) {
				$sql .= ' ORDER BY '.implode(', ', $c);
			}
		}
		if(!empty($params['limit']) && (intval($params['limit']) > 0)) {
			$sql .= ' LIMIT '.intval($params['limit']);
		}
		WowDataAccess::obj()->log(__METHOD__.' >>> MySQL Load: '.htmlspecialchars($sql));
		$result = mysql_query($sql, $this->dblink);
		if($result === false) {
			WowDataAccess::obj()->error(__METHOD__.' >>> MySQL Error: '.mysql_error($this->dblink).' ('.$sql.')', 500);
		} else {
			if(mysql_num_rows($result) > 0) {
				$c = array();
				while($row = mysql_fetch_assoc($result)) {
					$c[] = $row;
				}
				return($c);
			}
		}
		return(false);
	}
	
	/**
	 * Builds an INSERT / UPDATE query and runs it based on the passed parameters
	 * @param array $params associative array containing the information needed to build the MySQL statement
	 * @return boolean true on success, false on failure
	 */
	protected function runSaveSQL(array $params = array()) {
		$sql = 'INSERT INTO `'.mysql_real_escape_string($params['table'], $this->dblink).'`';
		if(!empty($params['set'])) {
			$fields = array();
			$c      = array();
			$odku   = array();
			foreach($params['set'] as $pparams) {
				$subc = array();
				foreach($pparams as $key => $value) {
					if(count($fields) < count($pparams)) {
						$fields[] = '`'.mysql_real_escape_string($key, $this->dblink).'`';
					}
					$subc[] = '\''.mysql_real_escape_string($value, $this->dblink).'\'';
					if(count($odku) < count($pparams)) {
						$odku[] = '`'.mysql_real_escape_string($key, $this->dblink).'` = VALUES(`'.mysql_real_escape_string($key, $this->dblink).'`)';
					}
				}
				$c[] = '('.implode(', ', $subc).')';
			}
			if(!empty($fields) && !empty($c)) {
				$sql .= ' ('.implode(', ', $fields).') VALUES '.implode(', ', $c).' ON DUPLICATE KEY UPDATE '.implode(', ', $odku);
			}
			WowDataAccess::obj()->log(__METHOD__.' >>> MySQL Save: '.htmlspecialchars($sql));
			$result = mysql_query($sql, $this->dblink);
			if($result === false) {
				WowDataAccess::obj()->error(__METHOD__.' >>> MySQL Error: '.mysql_error($this->dblink).' ('.$sql.')', 500);
				return(false);
			} else {
				return(true);
			}
		}
		return(false);
	}
}

?>