<?php
/***************************************************************
 * Extension Manager/Repository config file for ext "sr_language_menu".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language Selection',
	'description' => 'A plugin to display a list of languages to select from. Clicking on a language links to the corresponding version of the page.',
	'category' => 'plugin',
	'version' => '6.1.0',
	'state' => 'stable',
	'uploadfolder' => FALSE,
	'createDirs' => '',
	'clearcacheonload' => FALSE,
	'author' => 'Stanislas Rolland',
	'author_email' => 'typo3(arobas)sjbr.ca',
	'author_company' => 'SJBR',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '6.2.0-7.99.99',
			'static_info_tables' => '6.2.1-'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		)
	)
);