<?php

/**
 * @file controllers/grid/AddThisStatisticsGridCellProvider.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AddThisStatisticsGridCellProvider
 * @ingroup plugins_generic_addThis
 *
 * @brief Base class for a cell provider that can retrieve labels for AddThis stats.
 */

namespace APP\plugins\generic\addThis\controllers\grid;

use PKP\controllers\grid\DataObjectGridCellProvider;
use PKP\controllers\grid\GridColumn;
use PKP\core\PKPString;

class AddThisStatisticsGridCellProvider extends DataObjectGridCellProvider {
	//
	// Template methods from GridCellProvider
	//
	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row \PKP\controllers\grid\GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$element =& $row->getData();
		$columnId = $column->getId();
		assert(!empty($columnId));
		switch ($columnId) {
			case 'url':
				return array('label' => '<a href="' . PKPString::stripUnsafeHtml($element['url']) . '" target="_blank">' . PKPString::stripUnsafeHtml($element['url']) . '</a>');
			case 'shares':
				return array('label' => $element['shares']);
		}
	}
}

