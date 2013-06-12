<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Extending TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbBackend until http://forge.typo3.org/issues/47192 is resolved
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbBackend'] = array(
    'className' => 'SJBR\SrLanguageMenu\Persistence\Generic\Storage\Typo3DbBackend'
);

// Include page TS configuration for new element wizard
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/PageTS/modWizards.txt">');

// Configuring the language menu plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	$_EXTKEY,
	// A unique name of the plugin in UpperCamelCase
	'LanguageMenu',
	// An array holding the controller-action-combinations that are accessible
	// The first controller and its first action will be the default
	array (
		'Menu' => 'index',
	),
	// An array of non-cachable controller-action-combinations (they must already be enabled)
	array(
		'Menu' => 'index',
	),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

?>