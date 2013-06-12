<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
* Class for updating the language menu flexform
*/
class ext_update {
	 
	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return string  HTML
	 */
	public function main() {
		$messages = array();	
		$messages[] = $this->updatePluginInstances();
		$messages[] = $this->updateTsTemplates();
		return implode('<br /><br />', $messages);
	}

	/**
	 * Updates the instances of the plugin in table tt_content
	 *
	 * @return string  HTML
	 */
	protected function updatePluginInstances() {

		$pluginInstances = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tt_content',
			'CType = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr('tx_srlanguagemenu_pi1', 'tt_content')
		);

		foreach ($pluginInstances as $row) {
			$update = array(
                    		'CType' => 'srlanguagemenu_languagemenu',
                    		'pi_flexform' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="settings.layout">
                    <value index="vDEF">' . $row['tx_srlanguagemenu_type'] . '</value>
                </field>
                <field index="settings.languages">
                    <value index="vDEF">' . $row['tx_srlanguagemenu_languages'] . '</value>
                </field>
                <field index="settings.languageTitle">
                    <value index="vDEF">0</value>
                </field>
            </language>
        </sheet>
        <sheet index="sTemplate">
            <language index="lDEF">
                <field index="view.templateRootPath">
                    <value index="vDEF">EXT:sr_language_menu/Resources/Private/Templates/</value>
                </field>
                <field index="view.partialRootPath">
                    <value index="vDEF">EXT:sr_language_menu/Resources/Private/Templates/Partials</value>
                </field>
                <field index="view.layoutRootPath">
                    <value index="vDEF">EXT:sr_language_menu/Resources/Private/Templates/Layouts</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>'
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'uid=' . intval($row['uid']), $update);
		}

		$message = count($pluginInstances) . ' language menu elements were updated.';
		return $message;
	}

	/**
	 * Updates the TypoScript templates replacing tx_srlanguagemenu_pi1 by tx_srlanguagemenu
	 *
	 * @return string  HTML
	 */
	protected function updateTsTemplates() {

		$tsTemplates = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'sys_template',
			'1=1'
		);

		foreach ($tsTemplates as $row) {
			$update = array(
                    		'constants' => str_replace('tx_srlanguagemenu_pi1', 'tx_srlanguagemenu', $row['constants']),
                    		'config' => str_replace('plugin.tx_srlanguagemenu_pi1', 'plugin.tx_srlanguagemenu.settings', $row['config'])
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_template', 'uid=' . intval($row['uid']), $update);
		}

		$message = count($tsTemplates) . ' TypoScript templates were updated. Please verify the plugin configuration. You may need to move array _LOCAL_LANG from plugin.tx_srlanguagemenu.settings to plugin.tx_srlanguagemenu.';
		return $message;
	}

	public function access() {
		return TRUE;
	}
}
?>
