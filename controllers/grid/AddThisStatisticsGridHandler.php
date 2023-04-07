<?php

/**
 * @file controllers/grid/AddThisStatisticsGridHandler.php
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2000-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AddThisStatisticsGridHandler
 * @ingroup plugins_generic_addThis
 *
 * @brief Handle addThis plugin requests for statistics.
 */

namespace APP\plugins\generic\addThis\controllers\grid;

use PKP\controllers\grid\GridHandler;
use PKP\controllers\grid\GridColumn;
use PKP\security\authorization\ContextAccessPolicy;
use PKP\file\FileWrapper;
use PKP\security\Role;
use APP\core\Application;
use APP\plugins\generic\addThis\AddThisPlugin;
use Exception;

class AddThisStatisticsGridHandler extends GridHandler {
	protected AddThisPlugin $_plugin;

	/**
	 * Constructor
	 */
	function __construct(AddThisPlugin $plugin) {
		parent::__construct();
		$this->addRoleAssignment(
			[Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
			['fetchGrid', 'fetchRow']
		);
		$this->_plugin = $plugin;
	}


	//
	// Overridden methods from PKPHandler
	//
	/**
	 * @see PKPHandler::authorize()
	 * @param $request PKPRequest
	 * @param $args array
	 * @param $roleAssignments array
	 */
	function authorize($request, &$args, $roleAssignments) {
		$this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @copydoc GridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		// Basic grid configuration

		$this->setTitle('plugins.generic.addThis.grid.title');

		// Columns
		$cellProvider = new AddThisStatisticsGridCellProvider();
		$gridColumn = new GridColumn(
			'url',
			'common.url',
			null,
			null,
			$cellProvider,
			array('width' => 50, 'alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT)
		);

		$gridColumn->addFlag('html', true);

		$this->addColumn($gridColumn);

		$this->addColumn(
			new GridColumn(
				'shares',
				'plugins.generic.addThis.grid.shares',
				null,
				null,
				$cellProvider
			)
		);
	}


	//
	// Overridden methods from GridHandler
	//
	/**
	 * @see GridHandler::getRowInstance()
	 * @return AddThisStatisticsGridRow
	 */
	function getRowInstance() {
		return new AddThisStatisticsGridRow();
	}

	/**
	 * @copydoc GridHandler::loadData
	 */
	function loadData($request, $filter = null) {
		$context = $request->getContext();

		$addThisProfileId = $context->getData('addThisProfileId');
		$addThisUsername = $context->getData('addThisUsername');
		$addThisPassword = $context->getData('addThisPassword');

		$data = [];

		if (isset($addThisProfileId) && isset($addThisUsername) && isset($addThisPassword)) {
			$topSharedUrls = 'https://api.addthis.com/analytics/1.0/pub/shares/url.json?period=week&pubid='.urlencode($addThisProfileId).
				'&username='.urlencode($addThisUsername).
				'&password='.urlencode($addThisPassword);

			try {
				$jsonData = Application::get()->getHttpClient()->request('GET', $topSharedUrls)->getBody();

				if ($jsonData != '') {
					$jsonMessage = json_decode($jsonData);
					foreach ($jsonMessage as $statElement) {
						$data[] = ['url' => $statElement->url, 'shares' => $statElement->shares];
					}
				}
			} catch (Exception $e) {
				error_log('Unable to fetch the AddThis data for context ' . $context->getPath() . '. Check the AddThis plugin credentials and try again.');
			}
		}
		return $data;
	}
}

