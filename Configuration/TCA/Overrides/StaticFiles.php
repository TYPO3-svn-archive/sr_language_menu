<?php
defined('TYPO3_MODE') or die();

// Register language menu static templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sr_language_menu', 'Configuration/TypoScript/PluginSetup', 'Language Menu Setup');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sr_language_menu', 'Configuration/TypoScript/DefaultStyles', 'Language Menu CSS Styles');