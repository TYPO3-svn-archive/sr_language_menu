<?php
namespace SJBR\SrLanguageMenu\Xclass\Extbase\Persistence\Generic;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2013 Extbase Team (http://forge.typo3.org/projects/typo3v4-mvc)
 *  Extbase is a backport of TYPO3 Flow. All credits go to the TYPO3 Flow team.
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Query settings. This class is NOT part of the FLOW3 API.
 * It reflects the settings unique to TYPO3 4.x.
 *
 * @api
 */
class Typo3QuerySettings extends \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings {

	/**
	 * Flag if the language overlay should be prevented (default is FALSE).
	 *
	 * @var boolean
	 */
	protected $preventLanguageOverlay = FALSE;

	/**
	 * Sets the flag if a language overlay should be prevented.
	 *
	 * @param boolean $preventLanguageOverlay TRUE if a language overlay should be prevented.
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface (fluent interface)
	 * @api
	 */
	public function setPreventLanguageOverlay($preventLanguageOverlay) {
		$this->preventLanguageOverlay = $preventLanguageOverlay;
		return $this;
	}

	/**
	 * Returns the state, if a language overlay should be prevented.
	 *
	 * @return boolean TRUE, if a language overlay should be prevented; otherwise FALSE.
	 */
	public function getPreventLanguageOverlay() {
		return $this->preventLanguageOverlay;
	}
}

?>