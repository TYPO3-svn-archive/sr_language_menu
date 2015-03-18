<?php
defined('TYPO3_MODE') or die();

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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns);

$pluginSignature = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('sr_language_menu')) . '_languagemenu';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:' . 'sr_language_menu' . '/Configuration/FlexForms/form.xml', $pluginSignature);

$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] = '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ', --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.headers;headers';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ',--div--;LLL:EXT:sr_language_menu/Resources/Private/Language/locallang.xlf:settings.title;;;;3-3-3, pi_flexform';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ',--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access';
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .= ', --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames';
$GLOBALS['TCA']['tt_content']['ctrl']['typeicons'][$pluginSignature] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('sr_language_menu') . 'Resources/Public/Images/language.png';