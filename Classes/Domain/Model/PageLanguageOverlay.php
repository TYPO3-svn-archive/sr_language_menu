<?php
namespace SJBR\SrLanguageMenu\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * The Page Language Overlay model
 */
class PageLanguageOverlay extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * Page
	 *
	 * @var \SJBR\SrLanguageMenu\Domain\Model\Page
	 */
	protected $page;	

	/**
	 * Language
	 *
	 * @var \SJBR\SrLanguageMenu\Domain\Model\SystemLanguage
	 */
	protected $language;

	/**
	 * Sets the page
	 *
	 * @param \SJBR\SrLanguageMenu\Domain\Model\Page $page
	 * @return void
	 */
	public function setPage(\SJBR\SrLanguageMenu\Domain\Model\Page $page) {
		$this->page = $page;
	}	

	/**
	 * Returns the page
	 *
	 * @return \SJBR\SrLanguageMenu\Domain\Model\Page
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the language
	 *
	 * @param \SJBR\SrLanguageMenu\Domain\Model\SystemLanguage $language
	 * @return void
	 */
	public function setLanguage(\SJBR\SrLanguageMenu\Domain\Model\SystemLanguage $language) {
		$this->language = $language;
	}

	/**
	 * Returns the language
	 *
	 * @return SystemLanguage
	 */
	public function getLanguage() {
		return $this->language;
	}
}
?>