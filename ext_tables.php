<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register language menu static templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/PluginSetup', 'Language Menu Setup');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/DefaultStyles', 'Language Menu CSS Styles');

/**
 * Registers the plugin to be listed in the Backend
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	$_EXTKEY,
	// A unique name of the plugin in UpperCamelCase
	'LanguageMenu',
	// A title shown in the backend dropdown field
	'LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:pi1_title'
	//'Language selection menu'
);
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 6001000) {
	\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');
}
$tempColumns = Array (
	'tx_srlanguagemenu_languages' => Array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.languages',		
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'sys_language',
			'size' => '5',
			'maxitems' => 50,
			'minitems' => 1,
			'show_thumbs' => 1,
		)
	),
	'tx_srlanguagemenu_type' => array(        
		'exclude' => 0,        
		'label' => 'LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.layout',        
		'config' => array(
			'type' => 'select',
			'items' => array(
				array('LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.layout.I.0', '0'),
				array('LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.layout.I.1', '1'),
				array('LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.layout.I.2', '2'),
			),
		),
	),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns, 1);

$pluginSignature = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY)) . '_languagemenu';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/form.xml', $pluginSignature);

$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] = '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ', --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.headers;headers';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ',--div--;LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.title;;;;3-3-3, pi_flexform';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ',--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ', --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames';
$GLOBALS['TCA']['tt_content']['ctrl']['typeicons'][$pluginSignature] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Images/language.png';

?>