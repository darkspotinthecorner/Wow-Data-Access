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
 * load.php, Inclusion router that will load all required files
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// load.php

$path = dirname(__FILE__) . '/';

/*
 * Load core libs
 */
require_once($path . 'core/WowData.lib.php');
require_once($path . 'core/WowDataChannel.lib.php');
require_once($path . 'core/WowDataAccess.lib.php');

/*
 * Load channel definitions
 */
require_once($path . 'channels/WowDataChannelBattleNet.class.php');
require_once($path . 'channels/WowDataChannelFilesystem.class.php');
require_once($path . 'channels/WowDataChannelMySQL.class.php');

/*
 * Load data definitions
 */
require_once($path . 'datas/WowDataCharacter.class.php');
require_once($path . 'datas/WowDataCharacterThumbnail.class.php');
require_once($path . 'datas/WowDataGuild.class.php');
require_once($path . 'datas/WowDataIcon.class.php');
require_once($path . 'datas/WowDataItem.class.php');
require_once($path . 'datas/WowDataItemEquipped.class.php');
require_once($path . 'datas/WowDataRealm.class.php');

?>