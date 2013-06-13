<?php
namespace SJBR\SrLanguageMenu\ViewHelpers\Widget;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * This ViewHelper renders a language menu.
 *
 * = Example =
 *
 * <code title="full configuration">
 * <languageMenu:widget.menu languages="0,2,3" layout="Flags" languageTitle="1" />
 * </code>
 *
 * The widget accepts three arguments:
 *	languages: the list of uid's of system language records you want to see in the menu;
 *	layout: a keyword for the layout you want the menu to be rendered with:
 *		Flags (a list of flags),
 *		Select (a selector box),
 *		Links (a list of links)
 *	languageTitle: the labels you want to use for the languages:
 *		0 (the name of the language localized in the language of the current page),
 *		1 (the name of the language in the language itself),
 *		2 (the name of the language as set in the system language record in the TYPO3 backend),
 *		3 (the ISO language and, possibly, country codes of the language) 
 */
class MenuViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var \SJBR\SrLanguageMenu\Controller\MenuController
	 */
	protected $controller;

	/**
	 * @param \SJBR\SrLanguageMenu\Controller\MenuController $controller
	 * @return void
	 */
	public function injectController(\SJBR\SrLanguageMenu\Controller\MenuController $controller) {
		$this->controller = $controller;
	}
	
	public function initialize() {
		$this->controllerContext->getRequest()->setControllerExtensionName('SrLanguageMenu');
	}

	/**
	 * @param string $languages
	 * @param string $layout
	 * @param integer $languageTitle	 
	 * @return string
	 */
	public function render($languages = NULL, $layout = NULL, $languageTitle = NULL) {
		return $this->initiateSubRequest();
	}
}

?>