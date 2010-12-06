<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2003 Kasper Skaarhoej (kasper@typo3.com)
*  (c) 2004-2010 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  (c) 2010-2011 Prakash A Bhat <spabhat(at)chandanweb.com>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Plugin 'Language Selection' for the 'sr_language_menu' extension.
 *
 * @author	Kasper Skaarhoej <kasper@typo3.com>
 * @coauthor	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @coauthor	Prakash A Bhat <spabhat(at)chandanweb.com>
 */

class tx_srlanguagemenu_utils {
	private $pObj; /* This holds a reference to the current instance of class.tx_srlanguagemenu_pi1 */
	private $conf;
	private $languagesUids;
	private $languagesInfoArr;
	 /**
    * Class constructor.
    */
	public function init(&$pObj){
		$this->pObj = $pObj;
		//Assign parent's config to internal config variable so we can get all configuration in normal Typo3 Coding style
		$this->conf = $this->pObj->conf;

		$this->staticInfo = t3lib_div::makeInstance('tx_staticinfotables_pi1');
		$this->staticInfo->init();

	}

	/**
	 * Builds An array containing basic information about languages.
	 * <p>
	 * Entire information like
	 * - UID,
	 * - URL,
	 *  - unprocessed language label
	 * - LanguageExternal URLs
	 *
	 * @return	Array		An array containing complete information about languages
	 */
	public function getLanguagesInfoArray(){

		$tableA = 'sys_language';
		$tableB = 'static_languages';

		/* This is the new Information array
		 *which replaces the different variables used to hold a specific language key property
		 * this contains all possible properties for the language key, such as
		 *		- status like (CURRENT, NORMAL, INACTIVE
		 *		- URL for current key
		 *		- label
		 *		-
		 */
		$languagesInfoArr = array();		

		$languagesUidsList = trim($this->pObj->cObj->data['tx_srlanguagemenu_languages']) ? trim($this->pObj->cObj->data['tx_srlanguagemenu_languages']) : trim($this->conf['languagesUidsList']);		

		// Set default language
			$defaultLanguageISOCode = trim($this->conf['defaultLanguageISOCode']) ?  strtoupper(trim($this->conf['defaultLanguageISOCode'])) : 'EN';
			$defaultCountryISOCode = trim($this->conf['defaultCountryISOCode']) ?  strtoupper(trim($this->conf['defaultCountryISOCode'])) : '';
			
			$languagesInfoArr[0]['languages'] = strtolower($defaultLanguageISOCode).($defaultCountryISOCode?'_'.$defaultCountryISOCode:'');

			$this->languagesUids[0] = '0';
			$languagesInfoArr[0]['languagesUid'] = 0;

			if ($useIsoLanguageCountryCode) {				
				$languagesInfoArr[0]['languagesLabel'] = strtolower($defaultLanguageISOCode).($defaultCountryISOCode?'-'.strtolower($defaultCountryISOCode):'');
			} else {				
				$languagesInfoArr[0]['languagesLabel'] = $this->staticInfo->getStaticInfoName('LANGUAGES', strtoupper($languagesInfoArr[0]['languages']),'','',$useSelfLanguageTitle);
				if (!$languagesInfoArr[0]['languagesLabel'] && $defaultCountryISOCode) {
					$languagesInfoArr[0]['languagesLabel'] = $this->staticInfo->getStaticInfoName('LANGUAGES', strtoupper($defaultLanguageISOCode),'','',$useSelfLanguageTitle);
				}
			}
		//end default language


		// Get the language codes and labels for the languages set in the plugin list
		$selectFields = $tableA . '.uid, ' . $tableA . '.title, ' . $tableB . '.lg_iso_2, ' . $tableB . '.lg_name_en, ' . $tableB . '.lg_country_iso_2';
		$table = $tableA . ' LEFT JOIN ' . $tableB . ' ON ' . $tableA . '.static_lang_isocode=' . $tableB . '.uid';
			// Ignore IN clause if language list is empty. This means that all languages found in the sys_language table will be used
		if (!empty($languagesUidsList)) {
			$whereClause = $tableA . '.uid IN (' . $languagesUidsList . ') ';
		} else {
			$whereClause = '1=1 ';
		}
		$whereClause .= $this->pObj->cObj->enableFields ($tableA);
		$whereClause .= $this->pObj->cObj->enableFields ($tableB);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectFields, $table, $whereClause);
			// If $languagesUidsList is not empty, the languages will be sorted in the order it specifies
		$languagesUidsArray = t3lib_div::trimExplode(',', $languagesUidsList, 1);
		$index = 0;	

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['lg_iso_2'] != $defaultLanguageISOCode || $row['lg_country_iso_2'] != $defaultCountryISOCode) {
				$index++;
				$key = array_search($row['uid'], $languagesUidsArray);
				$key = ($key !== FALSE) ? $key+1 : $index;
				
				$languagesInfoArr[$key]['languagesUid'] = $row['uid'];				
				$languagesInfoArr[$key]['languages'] = strtolower($row['lg_iso_2']).($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');

				if ($useIsoLanguageCountryCode) {
					$languagesInfoArr[$key]['languagesLabel'] =  strtolower($row['lg_iso_2']).($row['lg_country_iso_2']?'-'.strtolower($row['lg_country_iso_2']):'');
				} elseif ($useSysLanguageTitle) {					
					$languagesInfoArr[$key]['languagesLabel'] =  $row['title'];
				} else {					
					$languagesInfoArr[$key]['languagesLabel'] =  $this->staticInfo->getStaticInfoName('LANGUAGES', $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:''),'','',$useSelfLanguageTitle);
				}
			} elseif ($useSysLanguageTitle) {					
					$languagesInfoArr['0']['languagesLabel'] =  $row['title'];
			}
		}


		// Show current language first, if configured
		if ($this->conf['showCurrentFirst']) {			
			if ( array_key_exists($GLOBALS['TSFE']->sys_language_uid, $languagesInfoArr) ) {
				$key = $GLOBALS['TSFE']->sys_language_uid;
				$currentLanguageToBeShifted = $languagesInfoArr[$key];
				unset($languagesInfoArr[$key]);
				array_unshift($languagesInfoArr, $currentLanguageToBeShifted );				
			}
		}

			// Select all pages_language_overlay records on the current page. Each represents a possibility for a language.
		$langArr = array();
		$table = 'pages_language_overlay';
		$whereClause = 'pid=' . $GLOBALS['TSFE']->id . ' ';
		$whereClause .= $GLOBALS['TSFE']->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT sys_language_uid', $table, $whereClause);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$langArr[$row['sys_language_uid']] = $row['sys_language_uid'];
		}
			// Add configured external url's for missing overlay records.
		if (is_array($this->conf['useExternalUrl.'])) {
			foreach ($languagesInfoArr as $key => $val) {
				if ($languagesInfoArr[$key]) {
					if (!$langArr[$languagesInfoArr[$key]]) {
						if ($this->conf['useExternalUrl.'][$val]) {
							$languagesInfoArr[$key]['languagesExternalUrls'] = $this->conf['useExternalUrl.'][$val];
							$langArr[$languagesInfoArr[$key]] = $languagesInfoArr[$key];
						}
					} else {
						if ($this->conf['useExternalUrl.'][$val] && ($this->conf['useExternalUrl.'][$val. '.']['force'] || $this->conf['forceUseOfExternalUrl'])) {
							$languagesInfoArr[$key]['languagesExternalUrls'] = $this->conf['useExternalUrl.'][$val];
						}
					}
				}
			}
		}
		
		ksort($languagesInfoArr);
		$this->languagesInfoArr = $languagesInfoArr;


		echo print_r($langArr,1) . ': $langArr' . "\n\n";
		echo print_r($languages,1) . ': languages' . "\n\n";
		echo print_r($this->languagesUids,1) . ': languagesUids' . "\n\n";
		echo print_r($languagesLabels,1) . ': $languagesLabel' . "\n\n";
		echo print_r($languagesInfoArr,1) . ': $languagesInfoArr' . "\n\n";

		
		//at this instance we will need to prepare even the frontend display for each language 

		$this->processLaguageInfoArray();

		return $this->languagesInfoArr;

	}

	/**
	 * This function applies various properties to the language info array.
	 * <p>
	 * such as
	 * - applying proper label
	 * - applying stdWrap specfied in TS
	 * - Languge status like Current/Active, Normal and Inactive
	 * - Flag icon path (this should now also support PNG files)
	 *
	 * @return	Array		An array containing complete information about languages
	 */
	private function processLaguageInfoArray(){

		foreach ($this->languagesInfoArr as $key => $val) {
			$uri = $this->makeUrl($key, !$this->realUrlLoaded);
			if (!$this->languagesInfoArr[$key]['languagesUid'] ) {
				$names[$key][(($this->realUrlLoaded && !$this->languagesExternalUrls[$key]) ? $this->getWebsiteDir().$uri : $this->languagesInfoArr[$key]['languagesUid'])] = $this->languagesInfoArr[$key]['languagesLabel'];
				$selected = ($GLOBALS['TSFE']->sys_language_uid == $this->languagesInfoArr[$key]['languagesUid']) ? (($this->realUrlLoaded && !$this->languagesExternalUrls[$key]) ? $this->getWebsiteDir().$uri : $this->languagesInfoArr[$key]['languagesUid']) : $selected;
			}
		}		
	}


	/**
	 * Makes the url
	 *
	 * @param	string	$key: the ordinal number of the language for which an url should be made
	 * @param	boolean	$noLVariable: if set, the url is built without the L variable
	 * @return   	string  the url
	 */
	function makeUrl($key, $noLVariable=0) {
		if ($this->languagesInfoArr[$key]['languagesExternalUrls']) {
			return $this->languagesExternalUrls[$key];
		}
		if (strstr($GLOBALS['TSFE']->linkVars, '&L=')) {
			$GLOBALS['TSFE']->linkVars = preg_replace('/&L=[0-9]*/' , ($noLVariable ? '' : '&L='.$this->languagesInfoArr['languagesUid']), $GLOBALS['TSFE']->linkVars);
		} else {
			$GLOBALS['TSFE']->linkVars .= $noLVariable ? '' : '&L='.$this->languagesInfoArr['languagesUid'];
		}
		if (!$this->rlmp_language_detectionLoaded) {
			$GLOBALS['TSFE']->linkVars = preg_replace('/&L=0/' , '', $GLOBALS['TSFE']->linkVars);
		}
		$LD = $this->pObj->localTemplate->linkData($GLOBALS['TSFE']->page,'','','','',$this->forwardParams,'0');
		return $LD['totalURL'];
	}

	private function makeOutPut(){
		return false;
	}



}

?>