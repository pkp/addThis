<?php

/**
 * @file AddThisStatisticsGridRow.php
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2000-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AddThisStatisticsGridRow
 * @ingroup plugins_generic_addThis
 *
 * @brief AddThis statistics grid row definition
 */

namespace APP\plugins\generic\addThis\controllers\grid;

use PKP\controllers\grid\GridRow;

class AddThisStatisticsGridRow extends GridRow {
	//
	// Overridden methods from GridRow
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		// Do the default initialization
		parent::initialize($request, $template);

		// Is this a new row or an existing row?
		$statsRow = $this->_data;
		if ($statsRow != null) {
			// no grid actions for this.
			$this->setTemplate('controllers/grid/gridRow.tpl');
		}
	}
}

