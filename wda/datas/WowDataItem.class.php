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
 * WowDataItem.class.php, Item Data definition
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataItem.class.php

/**
 * Defined data class that represents a wow item
 * @package wow-data-access
 * @subpackage wow-data
 * @example
 * 		$item = new WowDataItem(
 * 			array(
 * 				'id'               => 63833,
 * 				'name'             => 'Dunwald Winged Helm',
 * 				'icon'             => 'inv_helmet_192',
 * 				'overallQualityId' => 3,
 * 				'bonding'          => 1,
 * 				'classId'          => 4,
 * 				'requiredLevel'    => -1,
 * 				'itemLevel'        => 333,
 * 				'damage'           => array(
 * 					array('min' => 10, 'max' => 33,  'type' => 'fire'),
 * 					array('min' => 99, 'max' => 133, 'type' => 'physical')
 * 				),
 * 				'sockets'          => array(
 * 					array('icon' => 'inv_misc_gem_121', 'color' => 'red',    'enchant' => '+30 Stamina',    'match' => true),
 * 					array('icon' => 'inv_misc_gem_101', 'color' => 'yellow', 'enchant' => '+30 Hit Rating', 'match' => false)
 * 				),
 * 				'allowableRaces'   => array('Dwarf', 'Gnome'), 
 * 				'allowableClasses' => array('Warrior', 'Palading', 'Death Knight'),
 * 				'requiredFaction'  => array('name' => 'Kirin Tor', 'reputation' => 12000),
 * 				'spellData'        => array('trigger' => 2, 'desc' => 'Teleports you to Dalaran, 30 min Cooldown.'),
 * 				'itemSetName'      => 'Dunwald Winged Stuff',
 * 				'itemSetItems'     => array(
 * 					array('name' => 'Dunwald Winged Helm',     'equipped' => true),
 * 					array('name' => 'Dunwald Winged Tunic',    'equipped' => false),
 * 					array('name' => 'Dunwald Winged Trousers', 'equipped' => false),
 * 					array('name' => 'Dunwald Winged Boots',    'equipped' => false),
 * 					array('name' => 'Dunwald Winged Bracers',  'equipped' => false),
 * 					array('name' => 'Dunwald Winged Gloves',   'equipped' => false),
 * 				),
 * 				'itemSetBonus'     => array(
 * 					array('threshold' => 2, 'desc' => 'You are now quite amazing!'),
 * 					array('threshold' => 4, 'desc' => 'You are now really amazing!'),
 * 					array('threshold' => 6, 'desc' => 'You are now totally awesome amazing!'),
 * 				),
 * 				
 * 				'desc'             => 'This item rocks...',
 * 			)
 * 		);
 * 		
 * 		$output = $item->get(array(
 * 			'Eidee'    => 'id',
 * 			'Nohme...' => 'name',
 * 			'Sockel'   => 'sockets'
 * 		));
 */
class WowDataItem extends WowDataBase {
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array_merge(parent::getMetaData(), array(
			// --- id -------------------------------------------- R -
			'id' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyId', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'itemid',
			),
			// --- name ------------------------------------------ R -
			'name' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 2, 256), 'feedback' => 'Item name must be between 2 and 256 characters long.'),
				),
				'clean'    => array(
					array('method' => 'cleanItemName', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- icon ------------------------------------------ R -
			'icon' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item icon must not be empty.'),
					array('method' => 'verifyFileName',     'params' => array(self::INPUT_TOKEN),    'feedback' => 'Item icon must not contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanFileName', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- overallQualityId ------------------------------ R -
			'overallQualityId' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item id must be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonding --------------------------------------- R -
			'bonding' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonding id must be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- classId --------------------------------------- R -
			'classId' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item class id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- itemLevel -----------------------------------------
			'itemLevel' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item required level must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- glyphType -----------------------------------------
			'glyphType' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Glyph type must not contain special characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- heroic --------------------------------------------
			'heroic' => array(
				'required' => false,
				'clean'    => array(
					array('method' => 'cleanBoolean', 'params' => array(true)),
				),
			),
			// --- conjured ------------------------------------------
			'conjured' => array(
				'required' => false,
				'clean'    => array(
					array('method' => 'cleanBoolean', 'params' => array(true)),
				),
			),
			// --- accountBound --------------------------------------
			'accountBound' => array(
				'required' => false,
				'clean'    => array(
					array('method' => 'cleanBoolean', 'params' => array(true)),
				),
			),
			// --- zoneBound -----------------------------------------
			'zoneBound' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item bound zone must not be empty.'),
				),
			),
			// --- instanceBound -------------------------------------
			'instanceBound' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item bound instance must not be empty.'),
				),
			),
			// --- stackable -----------------------------------------
			'stackable' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item stackable count be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- maxCount ------------------------------------------
			'maxCount' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item max count be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- uniqueEquippable ----------------------------------
			'uniqueEquippable' => array(
				'required' => false,
				'clean'    => array(
					array('method' => 'cleanBoolean', 'params' => array(true)),
				),
			),
			// --- startQuestId --------------------------------------
			'startQuestId' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyId', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item started quest id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- inventoryType -------------------------------------
			'inventoryType' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item inventory type must be a non-negative integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- subclassName --------------------------------------
			'subclassName' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item subclass name must not be empty.'),
				),
			),
			// --- containerSlots ------------------------------------
			'containerSlots' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyPositiveInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item contailer slots must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanPositiveInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- damageDps -----------------------------------------
			'damageDps' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyPositiveFloat', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item dps must be a positive float.'),
				),
				'clean'    => array(
					array('method' => 'cleanPositiveFloat', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- damageSpeed ---------------------------------------
			'damageSpeed' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyNonNegativeFloat', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item dps must be a non-negative float.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeFloat', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- damage --------------------------------------------
			'damage' => array(
				'required' => false,
				'collect'  => true,
				'subset'   => array(
					'min'  => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item component minimum damage must be a non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'max'  => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item component maximum damage must be a non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'type' => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item component damage type must be a non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
				),
			),
			// --- armor ---------------------------------------------
			'armor' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyPositiveInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item armory must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanPositiveInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- blockValue ----------------------------------------
			'blockValue' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyPositiveInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item block value must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanPositiveInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonusStrength -------------------------------------
			'bonusStrength' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonus strength must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonusAgility --------------------------------------
			'bonusAgility' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonus strength must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonusStamina --------------------------------------
			'bonusStamina' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonus strength must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonusIntellect ------------------------------------
			'bonusIntellect' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonus strength must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- bonusSpirit ---------------------------------------
			'bonusSpirit' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item bonus strength must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- enchant -------------------------------------------
			// --- randomEnchantData ---------------------------------
			// --- randomEnchantDataPrefix ---------------------------
			// --- randomEnchantDataSuffix ---------------------------
			// --- randomEnchantDataEnchant --------------------------
			// --- sockets -------------------------------------------
			'sockets' => array(
				'required' => false,
				'collect'  => true,
				'subset'   => array(
					'icon'  => array(
						'clean'    => array(
							array('method' => 'cleanFileName', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'color' => array(
						'verify'   => array(
							array('method' => 'verifyKey', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item socket color key must not contain special characters.'),
						),
						'clean'    => array(
							array('method' => 'cleanKey', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'enchant' => array(
						'clean'    => array(
							array('method' => 'cleanNoHTML', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'match' => array(
						'clean'    => array(
							array('method' => 'cleanBoolean', 'params' => array(self::INPUT_TOKEN)),
						),
					),
				),
			),
			// --- gemProperties -------------------------------------
			// --- durabilityMax -------------------------------------
			// --- durabilityCurrent ---------------------------------
			// --- allowableRaces ------------------------------------
			'allowableRaces' => array(
				'required' => false,
				'collect'  => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Allowable races entry must not be empty.'),
				),
			),
			// --- allowableClasses ----------------------------------
			'allowableClasses' => array(
				'required' => false,
				'collect'  => true,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Allowable classes entry must not be empty.'),
				),
			),
			// --- requiredLevel -------------------------------------
			'requiredLevel' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item required level must be an integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- requiredLevelMin ----------------------------------
			// --- requiredLevelMax ----------------------------------
			// --- requiredLevelCurr ---------------------------------
			// --- requiredAbility -----------------------------------
			// --- requiredPersonalArenaRating -----------------------
			// --- requiredFaction -----------------------------------
			'requiredFaction' => array(
				'required' => false,
				'subset'   => array(
					'name'  => array(
						'verify'   => array(
							array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Required faction name must not be empty.'),
						),
					),
					'reputation' => array(
						'verify'   => array(
							array('method' => 'verifyInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item required level must be an integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
				),
			),
			
			// --- bonusDefenseSkillRating ---------------------------
			// --- bonusDodgeRating ----------------------------------
			// --- bonusParryRating ----------------------------------
			// --- bonusBlockRating ----------------------------------
			// --- bonusHitSpellRating -------------------------------
			// --- bonusCritSpellRating ------------------------------
			// --- bonusHasteSpellRating -----------------------------
			// --- bonusHitRating ------------------------------------
			// --- bonusCritRating -----------------------------------
			// --- bonusResilienceRating -----------------------------
			// --- bonusHasteRating ----------------------------------
			// --- bonusSpellPower -----------------------------------
			// --- bonusAttackPower ----------------------------------
			// --- bonusRangedAttackPower ----------------------------
			// --- bonusFeralAttackPower -----------------------------
			// --- bonusArmorPenetration -----------------------------
			// --- bonusBlockValue -----------------------------------
			// --- bonusManaRegen ------------------------------------
			// --- bonusHealthRegen ----------------------------------
			// --- bonusSpellPenetration -----------------------------
			// --- bonusExpertiseRating ------------------------------
			// --- spellData -----------------------------------------
			'spellData' => array(
				'required' => false,
				'collect'  => true,
				'subset'   => array(
					'trigger'  => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Spell data trigger must be a non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'desc'  => array(
						'verify'   => array(
							array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 2), 'feedback' => 'Spell data description must not be empty.'),
						),
					),
					'charges'  => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Spell data charges must be non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'maxCharges'  => array(
						'verify'   => array(
							array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Spell data maximum charges must be non-negative integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
				),
			),
			// --- itemSetName ---------------------------------------
			'itemSetName' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item set name must not be empty.'),
				),
			),
			// --- itemSetItems --------------------------------------
			'itemSetItems' => array(
				'required' => false,
				'collect'  => true,
				'subset'   => array(
					'name'  => array(
						'verify'   => array(
							array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item set item name must not be empty.'),
						),
					),
					'equipped'  => array(
						'clean'    => array(
							array('method' => 'cleanBoolean', 'params' => array(self::INPUT_TOKEN)),
						),
					),
				),
			),
			// --- itemSetBonus --------------------------------------
			'itemSetBonus' => array(
				'required' => false,
				'collect'  => true,
				'subset'   => array(
					'threshold'  => array(
						'verify'   => array(
							array('method' => 'verifyPositiveInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Item set bonus threshold must be a positive integer.'),
						),
						'clean'    => array(
							array('method' => 'cleanPositiveInteger', 'params' => array(self::INPUT_TOKEN)),
						),
					),
					'desc'  => array(
						'verify'   => array(
							array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item set bonus description must not be empty.'),
						),
					),
				),
			),
			// --- desc ----------------------------------------------
			'desc' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyStringLength', 'params' => array(self::INPUT_TOKEN, 1), 'feedback' => 'Item description must not be empty.'),
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