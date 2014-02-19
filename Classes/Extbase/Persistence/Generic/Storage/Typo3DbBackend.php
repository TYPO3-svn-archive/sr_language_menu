<?php
namespace SJBR\SrLanguageMenu\Extbase\Persistence\Generic\Storage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2013 Extbase Team (http://forge.typo3.org/projects/typo3v4-mvc)
 *  Extbase is a backport of TYPO3 Flow. All credits go to the TYPO3 Flow team.
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
 * A Storage backend
 */
class Typo3DbBackend extends \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbBackend {

	/**
	 * Performs workspace and language overlay on the given row array. The language and workspace id is automatically
	 * detected (depending on FE or BE context). You can also explicitly set the language/workspace id.
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface $source The source (selector od join)
	 * @param array $rows
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings The TYPO3 CMS specific query settings
	 * @param null|integer $workspaceUid
	 * @return array
	 */
	protected function doLanguageAndWorkspaceOverlay(\TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface $source, array $rows, $querySettings, $workspaceUid = NULL) {
		if ($source instanceof \TYPO3\CMS\Extbase\Persistence\Generic\Qom\SelectorInterface) {
			$tableName = $source->getSelectorName();
		} elseif ($source instanceof \TYPO3\CMS\Extbase\Persistence\Generic\Qom\JoinInterface) {
			$tableName = $source->getRight()->getSelectorName();
		}
		// If we do not have a table name here, we cannot do an overlay and return the original rows instead.
		if (isset($tableName)) {
			$pageRepository = $this->getPageRepository();
			if (is_object($GLOBALS['TSFE'])) {
				$languageMode = $GLOBALS['TSFE']->sys_language_mode;
				if ($workspaceUid !== NULL) {
					$pageRepository->versioningWorkspaceId = $workspaceUid;
				}
			} else {
				$languageMode = '';
				if ($workspaceUid === NULL) {
					$workspaceUid = $GLOBALS['BE_USER']->workspace;
				}
				$pageRepository->versioningWorkspaceId = $workspaceUid;
			}

			$overlayedRows = array();
			foreach ($rows as $row) {
				// If current row is a translation select its parent
				if (!(method_exists($querySettings, 'getPreventLanguageOverlay' ) && $querySettings->getPreventLanguageOverlay())
					&& isset($tableName) && isset($GLOBALS['TCA'][$tableName])
					&& isset($GLOBALS['TCA'][$tableName]['ctrl']['languageField'])
					&& isset($GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'])
				) {
					if (isset($row[$GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField']])
						&& $row[$GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField']] > 0
					) {
						$row = $this->databaseHandle->exec_SELECTgetSingleRow(
							$tableName . '.*',
							$tableName,
							$tableName . '.uid=' . (integer) $row[$GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField']] .
								' AND ' . $tableName . '.' . $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] . '=0'
						);
					}
				}
				$pageRepository->versionOL($tableName, $row, TRUE);
				if ($pageRepository->versioningPreview && isset($row['_ORIG_uid'])) {
					$row['uid'] = $row['_ORIG_uid'];
				}
				if (!(method_exists($querySettings, 'getPreventLanguageOverlay' ) && $querySettings->getPreventLanguageOverlay())) {
					if ($tableName == 'pages') {
						$row = $pageRepository->getPageOverlay($row, $querySettings->getSysLanguageUid());
					} elseif (isset($GLOBALS['TCA'][$tableName]['ctrl']['languageField'])
						&& $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] !== ''
					) {
						if (in_array($row[$GLOBALS['TCA'][$tableName]['ctrl']['languageField']], array(-1, 0))) {
							$overlayMode = $languageMode === 'strict' ? 'hideNonTranslated' : '';
							$row = $pageRepository->getRecordOverlay($tableName, $row, $querySettings->getSysLanguageUid(), $overlayMode);
						}
					}
				}
				if ($row !== NULL && is_array($row)) {
					$overlayedRows[] = $row;
				}
			}
		} else {
			$overlayedRows = $rows;
		}
		return $overlayedRows;
	}
}

?>
