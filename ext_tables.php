<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
	/**
	 * Registers the plugin to be listed in the Backend
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
		'SJBR.' . $_EXTKEY,
		// A unique name of the plugin in UpperCamelCase
		'LanguageMenu',
		// A title shown in the backend dropdown field
		'LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:pi1_title'
		//'Language selection menu'
	);
}
