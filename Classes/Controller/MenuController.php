<?php
namespace SJBR\SrLanguageMenu\Controller;
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
 * Controls the rendering of the language menu as a normal content element or as a Fluid widget
 */
class MenuController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @var array
	 */
	protected $supportedRequestTypes = array(
		'TYPO3\\CMS\\Extbase\\Mvc\\Request',
		'TYPO3\\CMS\\Fluid\\Core\\Widget\\WidgetRequest'
	);
	
	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'SrLanguageMenu';

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionKey = 'sr_language_menu';

	/**
	 * @var \SJBR\SrLanguageMenu\Domain\Repository\SystemLanguageRepository
	 */
	protected $systemLanguageRepository;

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository
	 */
	protected $languageRepository;

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\PageRepository
	 */
	protected $pageRepository;

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\PageLanguageOverlayRepository
	 */
	protected $pageLanguageOverlayRepository;

 	/**
	 * Dependency injection of the System Language Repository
 	 *
	 * @param \SJBR\SrLanguageMenu\Domain\Repository\LanguageRepository $languageRepository
 	 * @return void
	 */
	public function injectSystemLanguageRepository(\SJBR\SrLanguageMenu\Domain\Repository\SystemLanguageRepository $systemLanguageRepository) {
		$this->systemLanguageRepository = $systemLanguageRepository;
	}

 	/**
	 * Dependency injection of the Static Language Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository $languageRepository
 	 * @return void
	 */
	public function injectLanguageRepository(\SJBR\StaticInfoTables\Domain\Repository\LanguageRepository $languageRepository) {
		$this->languageRepository = $languageRepository;
	}

	/**
	 * Dependency injection of the Page Repository
 	 *
	 * @param \SJBR\SrLanguageMenu\Domain\Repository\PageRepository $pageRepository
 	 * @return void
	 */
	public function injectPageRepository(\SJBR\SrLanguageMenu\Domain\Repository\PageRepository $pageRepository) {
		$this->pageRepository = $pageRepository;
	}

 	/**
	 * Dependency injection of the Page Language Overlay Repository
 	 *
	 * @param \SJBR\SrLanguageMenu\Domain\Repository\PageLanguageOverlayRepository $pageLanguageOverlayRepository
 	 * @return void
	 */
	public function injectPageLanguageOverlayRepository(\SJBR\SrLanguageMenu\Domain\Repository\PageLanguageOverlayRepository $pageLanguageOverlayRepository) {
		$this->pageLanguageOverlayRepository = $pageLanguageOverlayRepository;
	}

	/**
	 * Initialize the action when rendering as a widget
	 * @return void
	 */
	public function initializeAction() {
		if (is_array($this->widgetConfiguration)) {
			$this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, $this->extensionName);
			if (isset($this->widgetConfiguration['languages'])) {
				$this->settings['languages'] = $this->widgetConfiguration['languages'];
			}
			if (isset($this->widgetConfiguration['layout'])) {
				$this->settings['layout'] = $this->widgetConfiguration['layout'];
			}
			if (isset($this->widgetConfiguration['languageTitle'])) {
				$this->settings['languageTitle'] = $this->widgetConfiguration['languageTitle'];
			}
		}
	}

	/**
	 * Show the menu
	 *
	 * @return string empty string
	 */
	public function indexAction() {

		// Adjust settings
		$this->processSettings();

		// Get system languages
		$systemLanguages = $this->systemLanguageRepository->findAllByUidInList($this->settings['languages'])->toArray();
		// Add default language
		$defaultLanguageISOCode = $this->settings['defaultLanguageISOCode'] ?  strtoupper($this->settings['defaultLanguageISOCode']) : 'EN';
		$defaultCountryISOCode = $this->settings['defaultCountryISOCode'] ?  strtoupper($this->settings['defaultCountryISOCode']) : '';
		$defaultIsoLanguage = $this->languageRepository->findOneByIsoCodes($defaultLanguageISOCode, $defaultCountryISOCode);
		if (!is_object($defaultIsoLanguage)) {
			$defaultCountryISOCode = '';
			$defaultIsoLanguage = $this->languageRepository->findOneByIsoCodes($defaultLanguageISOCode);
			if (!is_object($defaultIsoLanguage)) {
				$defaultLanguageISOCode = 'EN';
				$defaultIsoLanguage = $this->languageRepository->findOneByIsoCodes($defaultLanguageISOCode);
			}
		}
		$defaultSystemLanguage = $this->objectManager->create('SJBR\\SrLanguageMenu\\Domain\\Model\\SystemLanguage');
		$defaultSystemLanguage->setIsoLanguage($defaultIsoLanguage);
		$defaultSystemLanguage->setTitle($defaultSystemLanguage->getIsoLanguage()->getNameLocalized());
		array_unshift($systemLanguages, $defaultSystemLanguage);

		// Get the available page language overlays
		$page = $this->pageRepository->findByUid($GLOBALS['TSFE']->id);
		$pageLanguageOverlays = $this->pageLanguageOverlayRepository->findByPage($page)->toArray();
		$availableOverlays = array();
		// Add default language
		$availableOverlays[] = 0;
		foreach ($pageLanguageOverlays as $pageLanguageOverlay) {
			$availableOverlays[] = $pageLanguageOverlay->getLanguage()->getUid();
		}
		
		// Do not show menu if hideIfNoAltLanguages is set and there are no alternate languages
		$this->settings['showMenu'] = !$this->settings['hideIfNoAltLanguages'] || (count($availableOverlays) > 1);
			
		// Build language options
		$options = array();
		// If $this->settings['languages'] is not empty, the languages will be sorted in the order it specifies
		$languages = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['languages'], TRUE);
		$index = 0;
		foreach ($systemLanguages as $systemLanguage) {
			$option = array(
				'uid' => $systemLanguage->getUid() ? $systemLanguage->getUid() : 0,
				'isoCodeA2' => $systemLanguage->getIsoLanguage()->getIsoCodeA2(),
				'countryIsoCodeA2' => $systemLanguage->getIsoLanguage()->getCountryIsoCodeA2()
			);
			// Set combined ISO code
			$option['combinedIsoCode'] = strtolower($option['isoCodeA2']) . ($option['countryIsoCodeA2'] ? '_' . $option['countryIsoCodeA2'] : '');

			// Set the label
			switch ($this->settings['languageTitle']) {
				case '0':
					$option['title'] = $systemLanguage->getIsoLanguage()->getNameLocalized();
					break;
				case '1':
					$option['title'] = $systemLanguage->getIsoLanguage()->getLocalName();
					break;
				case '2':
					$option['title'] = $systemLanguage->getTitle();
					break;
				case '3':
					$option['title'] = strtoupper($option['combinedIsoCode']);
					break;
			}
			
			// Set paths to flags
			$partialFlagFileName = $this->settings['flagsDirectory'] . ($this->settings['alternateFlags'][$option['combinedIsoCode']] ? $this->settings['alternateFlags'][$option['combinedIsoCode']] : $option['combinedIsoCode']);
			$option['flagFile'] = $partialFlagFileName . '.png';

			// Set availability of overlay
			$option['isAvailable'] = in_array($option['uid'], $availableOverlays);

			// Add configured external url for missing overlay record
			if ($this->settings['useExternalUrl'][$option['combinedIsoCode']] || is_array($this->settings['useExternalUrl'][$option['combinedIsoCode']])) {
				if ($option['isAvailable']) {
					if ($this->settings['forceUseOfExternalUrl'] || $this->settings['useExternalUrl'][$option['combinedIsoCode']]['force']) {
						$option['externalUrl'] = is_array($this->settings['useExternalUrl'][$option['combinedIsoCode']]) ? $this->settings['useExternalUrl'][$option['combinedIsoCode']]['_typoScriptNodeValue'] : $this->settings['useExternalUrl'][$option['combinedIsoCode']];
					}
				} else {
					$option['externalUrl'] = is_array($this->settings['useExternalUrl'][$option['combinedIsoCode']]) ? $this->settings['useExternalUrl'][$option['combinedIsoCode']]['_typoScriptNodeValue'] : $this->settings['useExternalUrl'][$option['combinedIsoCode']];
					$option['isAvailable'] = TRUE;
				}
			}

			// Set current language indicator
			$option['isCurrent'] = ($option['uid'] == $GLOBALS['TSFE']->sys_language_uid);

			// If $this->settings['languages'] is not empty, the languages will be sorted in the order it specifies
			if ($option['isoCodeA2'] != $defaultLanguageISOCode || $option['countryIsoCodeA2'] != $defaultCountryISOCode) {
				$index++;
				$key = array_search($option['uid'], $languages);
				$key = ($key !== FALSE) ? $key+1 : $index;
				$options[$key] = $option;
			} else {
				$options[0] = $option;
			}
		}
		ksort($options);

		// Show current language first, if configured
		if ($this->settings['showCurrentFirst']) {
			$key = array_search($GLOBALS['TSFE']->sys_language_uid, $languages);
			if ($key) {
				$option = $options[$key];
				unset($options[$key]);
				array_unshift($options, $option);
			}
		}

		// Render the menu
		$this->view->assign('settings', $this->settings);
		$this->view->assign('options', $options);
	}

	/**
	 * Reviews and adjusts plugin settings
	 *
	 * @return void
	 * @api
	 */
	protected function processSettings() {
		// Backward compatibility settings
		if (!isset($this->settings['languages'])) {
			$this->settings['languages'] = $this->settings['languagesUidsList'];
		}
		if (!isset($this->settings['languageTitle']) || !in_array($this->settings['languageTitle'], array(0, 1, 2, 3))) {
			if ($this->settings['useSysLanguageTitle']) {
				$this->settings['languageTitle'] = 2;
			} else if ($this->settings['useIsoLanguageCountryCode']) {
				$this->settings['languageTitle'] = 3;
			} else if ($this->settings['useSelfLanguageTitle']) {
				$this->settings['languageTitle'] = 1;
			} else {
				$this->settings['languageTitle'] = 0;
			}
		}
		
		// Map numeric layout to keyword
		if (!isset($this->settings['layout'])) {
			$this->settings['layout'] = $this->settings['defaultLayout'];
		}
		$allowedLayouts = array('Flags', 'Select', 'Links');
		// Allow keyword values coming from Fluid widget... and perhaps from TS setup
		if (!in_array($this->settings['layout'], $allowedLayouts)) {
			$this->settings['layout'] = $allowedLayouts[$this->settings['layout']];
			if (!$this->settings['layout']) {
				$this->settings['layout'] = 'Flags';
			}
		}

		// Flags directory
		$this->settings['flagsDirectory'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->extensionKey) . 'Resources/Public/Images/Flags/';
		if ($this->settings['englishFlagFile']) {
			$this->settings['flagsDirectory'] = dirname($GLOBALS['TSFE']->tmpl->getFileName(trim($this->settings['englishFlagFile']))) . '/';
		}

		// 'Hide default translation of page' configuration option
		$this->settings['hideIfDefaultLanguage'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hideIfDefaultLanguage($GLOBALS['TSFE']->page['l18n_cfg']);
		
		// Add L to url parameters to remove
		$this->settings['removeParams'] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['removeParams'], 1);
		$this->settings['removeParams'] = array_merge($this->settings['removeParams'], array('L'));
	}

	/**
	 * Handles a request. The result output is returned by altering the given response.
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request The request object
	 * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response The response, modified by this handler
	 * @return void
	 * @api
	 */
	public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
		if (method_exists($request, 'getWidgetContext')) {
			$this->widgetConfiguration = $request->getWidgetContext()->getWidgetConfiguration();
		}
		\TYPO3\CMS\Extbase\Mvc\Controller\ActionController::processRequest($request, $response);
	}

	/**
	 * Allows the widget template root path to be overriden via the framework configuration,
	 * e.g. plugin.tx_extension.view.widget.<WidgetViewHelperClassName>.templateRootPath
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
	 * @return void
	 */
	protected function setViewConfiguration(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
		if (method_exists($this->request, 'getWidgetContext')) {
			$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			$widgetViewHelperClassName = $this->request->getWidgetContext()->getWidgetViewHelperClassName();
			if (isset($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']) && strlen($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']) > 0 && method_exists($view, 'setTemplateRootPath')) {
				$view->setTemplateRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath']));
			}
		} else {
			\TYPO3\CMS\Extbase\Mvc\Controller\ActionController::setViewConfiguration($view);
		}
	}
}
class_alias('SJBR\SrLanguageMenu\Controller\MenuController', 'Tx_SrLanguageMenu_Controller_MenuController');
?>