<?php
namespace SJBR\SrLanguageMenu\Domain\Model;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * The System Language model
 */
class SystemLanguage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
	 * @var string System language name
	 */
	protected $title = '';

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Model\Language
	 */
	protected $isoLanguage;

	/**
	 * Sets the language name
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Gets the backend language name
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the ISO language
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\Language $isoLanguage
	 * @return void
	 */
	public function setIsoLanguage($isoLanguage) {
		$this->isoLanguage = $isoLanguage;
	}

	/**
	 * Gets the ISO language
	 *
	 * @return \SJBR\StaticInfoTables\Domain\Model\Language
	 */
	public function getIsoLanguage() {
		return clone $this->isoLanguage;
	}
}
?>