<?php

/**
 * @file plugins/generic/addThis/AddThisSettingsForm.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AddThisSettingsForm
 * @ingroup plugins_generic_AddThis
 *
 * @brief Form for adding/editing the settings for the AddThis plugin
 */

import('lib.pkp.classes.form.Form');

class AddThisSettingsForm extends Form {
	/** @var Context The press associated with the plugin being edited */
	var $_context;

	/** @var AddThisBlockPlugin The plugin being edited */
	var $_plugin;

	/**
	 * Constructor.
	 * @param $plugin AddThisBlockPlugin
	 * @param $context Context
	 */
	function __construct($plugin, $context) {
		parent::__construct($plugin->getTemplatePath() . 'settings.tpl');
		$this->setContext($context);
		$this->setPlugin($plugin);

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	//
	// Getters and Setters
	//
	/**
	 * Get the Context.
	 * @return Context
	 */
	function getContext() {
		return $this->_context;
	}

	/**
	 * Set the Context.
	 * @param Context
	 */
	function setContext($context) {
		$this->_context = $context;
	}

	/**
	 * Get the plugin.
	 * @return AddThisBlockPlugin
	 */
	function getPlugin() {
		return $this->_plugin;
	}

	/**
	 * Set the plugin.
	 * @param AddThisBlockPlugin $plugin
	 */
	function setPlugin($plugin) {
		$this->_plugin = $plugin;
	}

	//
	// Overridden template methods
	//
	/**
	 * Initialize form data from the plugin.
	 */
	function initData() {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		if (isset($plugin)) {
			$this->_data = array(
				'addThisProfileId' => $context->getSetting('addThisProfileId'),
				'addThisUsername' => $context->getSetting('addThisUsername'),
				'addThisPassword' => $context->getSetting('addThisPassword'),
				'addThisDisplayStyle' => $context->getSetting('addThisDisplayStyle'),
			);
		}
	}

	/**
	 * Fetch the form.
	 * @see Form::fetch()
	 * @param $request PKPRequest
	 */
	function fetch($request) {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $plugin->getName());
		$templateMgr->assign('pluginBaseUrl', $request->getBaseUrl() . '/' . $plugin->getPluginPath());

		$displayStyles = array(
			'small_toolbox' => 'img/toolbox-small.png',
			'plus_one_share_counter' => 'img/plusone-share.gif',
			'small_toolbox_with_share' => 'img/small-toolbox.jpg',
			'large_toolbox' => 'img/toolbox-large.png',
			'simple_button' => 'img/share.jpg',
			'button' => 'img/button.jpg',
		);

		$templateMgr->assign('displayStyles', $displayStyles);

		foreach ($this->_data as $key => $value) {
			$templateMgr->assign($key, $value);
		}

		return $templateMgr->fetch($plugin->getTemplatePath() . 'settings.tpl');
	}

	/**
	 * Assign form data to user-submitted data.
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array(
			'addThisDisplayStyle',
			'addThisUsername',
			'addThisPassword',
			'addThisProfileId',
		));
	}

	/**
	 * Save the plugin's data.
	 * @see Form::execute()
	 */
	function execute() {
		$plugin = $this->getPlugin();
		$context = $this->getContext();

		$context->updateSetting('addThisDisplayStyle', trim($this->getData('addThisDisplayStyle'), "\"\';"), 'string');
		$context->updateSetting('addThisProfileId', trim($this->getData('addThisProfileId'), "\"\';"), 'string');
		$context->updateSetting('addThisUsername', trim($this->getData('addThisUsername'), "\"\';"), 'string');
		$context->updateSetting('addThisPassword', trim($this->getData('addThisPassword'), "\"\';"), 'string');
	}
}
?>
