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
 * WowDataCharacter.class.php, Character Data definition
 * 
 * @author Martin Gelder <darkspotinthecorner {at} gmail {dot} com>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3
 * @version 1.0
 * @package wow-data-access
 */
// WowDataCharacter.class.php

/**
 * Defined data class that represents a wow character
 * @package wow-data-access
 * @subpackage wow-data
 */
class WowDataCharacter extends WowDataBase {
	/**
	 * Returns the meta data added by this class
	 * @return array associative array containing the meta data
	 */
	static public function getMetaData() {
		return(array_merge(parent::getMetaData(), array(
			// --- locale ---------------------------------------- R - Inherited from WowDataBase
			// --- region ---------------------------------------- R - Inherited from WowDataBase
			// --- realm ----------------------------------------- R -
			'realm' => array(
				'required' => true,
				'param'    => 'realm',
			),
			// --- name ------------------------------------------ R -
			'name' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyStringLength',     'params' => array(self::INPUT_TOKEN, 3, 16),  'feedback' => 'Character name must be between 3 and 16 characters length.'),
					array('method' => 'verifyCharacterName',    'params' => array(self::INPUT_TOKEN),         'feedback' => 'Character name must not contain special characters.'),
					array('method' => 'verifyMaxRepeatedChars', 'params' => array(self::INPUT_TOKEN, 2),      'feedback' => 'Character name must not contain more than two identical consecutive characters.'),
				),
				'clean'    => array(
					array('method' => 'cleanCharacterName', 'params' => array(self::INPUT_TOKEN)),
				),
				'param'    => 'character',
			),
			/*
			 * CORE
			 */
			// --- classid --------------------------------------- R -
			'classid' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyId', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Class id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- raceid ---------------------------------------- R -
			'raceid' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyId', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Race id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- genderid -------------------------------------- R -
			'genderid' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyId', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Gender id must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanId', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- level ----------------------------------------- R -
			'level' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyPositiveInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Level must be a positive integer.'),
				),
				'clean'    => array(
					array('method' => 'cleanPositiveInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- achievementPoints ----------------------------- R -
			'achievementPoints' => array(
				'required' => true,
				'verify'   => array(
					array('method' => 'verifyNonNegativeInteger', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Achievement points must be zero or more.'),
				),
				'clean'    => array(
					array('method' => 'cleanNonNegativeInteger', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			// --- thumbnail -----------------------------------------
			'thumbnail' => array(
				'required' => false,
				'verify'   => array(
					array('method' => 'verifyFilePath', 'params' => array(self::INPUT_TOKEN), 'feedback' => 'Thumbnail must be a valid file path.'),
				),
				'clean'    => array(
					array('method' => 'cleanFilePath', 'params' => array(self::INPUT_TOKEN)),
				),
			),
			
			
			/*
			 * STATS
			 */
			// --- itemLevelAverage ----------------------------------
			// --- itemLevelEquippedAverage --------------------------
			// --- strengthBase --------------------------------------
			// --- strengthBonus -------------------------------------
			// --- strengthIncAttackPower ----------------------------
			// --- agilityBase ---------------------------------------
			// --- agilityBonus --------------------------------------
			// --- agilityIncCritChance ------------------------------
			// --- staminaBase ---------------------------------------
			// --- staminaBonus --------------------------------------
			// --- staminaIncHealth ----------------------------------
			// --- intellectBase -------------------------------------
			// --- intellectBonus ------------------------------------
			// --- intellectIncMana ----------------------------------
			// --- intellectIncCritChance ----------------------------
			// --- spiritBase ----------------------------------------
			// --- spiritBonus ---------------------------------------
			// --- spiritIncManareg ----------------------------------
			// --- masteryBase ---------------------------------------
			// --- masteryBonus --------------------------------------
			// --- masteryBonusRating --------------------------------
			// --- healthMaximum -------------------------------------
			// --- manaMaximum ---------------------------------------
			// --- rageMaximum ---------------------------------------
			// --- energyMaximum -------------------------------------
			// --- focusMaximum --------------------------------------
			/*
			 * DEFENSE
			 */
			// --- armour --------------------------------------------
			// --- armourDamageReduction -----------------------------
			// --- dodgeTotal ----------------------------------------
			// --- dodgeBonus ----------------------------------------
			// --- dodgeBonusRating ----------------------------------
			// --- parryTotal ----------------------------------------
			// --- parryBonus ----------------------------------------
			// --- parryBonusRating ----------------------------------
			// --- blockChanceTotal ----------------------------------
			// --- blockChanceBonus ----------------------------------
			// --- blockChanceBonusRating ----------------------------
			// --- blockReduction ------------------------------------
			// --- resilienceTotal -----------------------------------
			// --- resilienceDamageReduction -------------------------
			/*
			 * RESISTANCE
			 */
			// --- resistanceArcane ----------------------------------
			// --- resistanceArcaneDamageReduction -------------------
			// --- resistanceFire ------------------------------------
			// --- resistanceFireDamageReduction ---------------------
			// --- resistanceFrost -----------------------------------
			// --- resistanceFrostDamageReduction --------------------
			// --- resistanceNature ----------------------------------
			// --- resistanceNatureDamageReduction -------------------
			// --- resistanceShadow ----------------------------------
			// --- resistanceShadowDamageReduction -------------------
			/*
			 * MELEE
			 */
			// --- meleeDamageMainhandMin ----------------------------
			// --- meleeDamageMainhandMax ----------------------------
			// --- meleeDamageMainhandSpeed --------------------------
			// --- meleeDamageMainhandDps ----------------------------
			// --- meleeDamageOffhandMin -----------------------------
			// --- meleeDamageOffhandMax -----------------------------
			// --- meleeDamageOffhandSpeed ---------------------------
			// --- meleeDamageOffhandDps -----------------------------
			// --- meleeAttackPowerBase ------------------------------
			// --- meleeAttackPowerBonus -----------------------------
			// --- meleeAttackPowerIncDps ----------------------------
			// --- meleeHasteTotal -----------------------------------
			// --- meleeHasteBonus -----------------------------------
			// --- meleeHasteBonusRating -----------------------------
			// --- meleeHitChanceTotal -------------------------------
			// --- meleeHitChanceBonus -------------------------------
			// --- meleeHitChanceBonusRating -------------------------
			// --- meleeCritChanceTotal ------------------------------
			// --- meleeCritChanceBonus ------------------------------
			// --- meleeCritChanceBonusRating ------------------------
			// --- meleeExpertiseTotal -------------------------------
			// --- meleeExpertiseTotalDodgeChanceReduction -----------
			// --- meleeExpertiseTotalParryChanceReduction -----------
			// --- meleeExpertiseBonus -------------------------------
			// --- meleeExpertiseBonusRating -------------------------
			/*
			 * RANGED
			 */
			// --- rangedDamageMin -----------------------------------
			// --- rangedDamageMax -----------------------------------
			// --- rangedDamageSpeed ---------------------------------
			// --- rangedDamageDps -----------------------------------
			// --- rangedAttackPowerBase -----------------------------
			// --- rangedAttackPowerBonus ----------------------------
			// --- rangedAttackPowerIncDps ---------------------------
			// --- rangedHasteTotal ----------------------------------
			// --- rangedHasteBonus ----------------------------------
			// --- rangedHasteBonusRating ----------------------------
			// --- rangedHitChanceTotal ------------------------------
			// --- rangedHitChanceBonus ------------------------------
			// --- rangedHitChanceBonusRating ------------------------
			// --- rangedCritChanceTotal -----------------------------
			// --- rangedCritChanceBonus -----------------------------
			// --- rangedCritChanceBonusRating -----------------------
			/*
			 * SPELL
			 */
			// --- spellPower ----------------------------------------
			// --- spellHasteTotal -----------------------------------
			// --- spellHasteBonus -----------------------------------
			// --- spellHasteBonusRating -----------------------------
			// --- spellHitChanceTotal -------------------------------
			// --- spellHitChanceBonus -------------------------------
			// --- spellHitChanceBonusRating -------------------------
			// --- spellCritChanceTotal ------------------------------
			// --- spellCritChanceBonus ------------------------------
			// --- spellCritChanceBonusRating ------------------------
			// --- spellPenetrationTotal -----------------------------
			// --- spellPenetrationTotalResistanceReduction ----------
			// --- spellPenetrationBonus -----------------------------
			// --- spellManaRegenNoCombat ----------------------------
			// --- spellManaRegenCombat ------------------------------
			/*
			 * SPEC
			 */
			// --- primarySpecMainTree -------------------------------
			// --- primarySpecTree1 ----------------------------------
			// --- primarySpecTree2 ----------------------------------
			// --- primarySpecTree3 ----------------------------------
			// --- primarySpecGlyphs ---------------------------------
			// --- secondarySpecMainTree -----------------------------
			// --- secondarySpecTree1 --------------------------------
			// --- secondarySpecTree2 --------------------------------
			// --- secondarySpecTree3 --------------------------------
			// --- secondarySpecGlyphs -------------------------------
			/*
			 * PVP
			 */
			// --- battlegroundRating --------------------------------
			// --- honorableKills ------------------------------------
			/*
			 * PROFESSION
			 */
			// --- primaryProfessionKey ------------------------------
			// --- primaryProfessionSkill ----------------------------
			// --- primaryProfessionSkillMax -------------------------
			// --- secondaryProfessionKey ----------------------------
			// --- secondaryProfessionSkill --------------------------
			// --- secondaryProfessionSkillMax -----------------------
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