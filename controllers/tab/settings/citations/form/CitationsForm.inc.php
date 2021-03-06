<?php

/**
 * @file controllers/tab/settings/citations/form/CitationsForm.inc.php
 *
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CitationsForm
 * @ingroup controllers_tab_settings_citations_form
 *
 * @brief Settings for the Citation Assistant.
 */

import('lib.pkp.classes.controllers.tab.settings.form.ContextSettingsForm');

class CitationsForm extends ContextSettingsForm {
	/**
	 * Constructor.
	 */
	function CitationsForm($wizardMode = false) {
		$settings = array(
			'metaCitations' => 'bool',
			'metaCitationOutputFilterId' => 'int'
		);
		parent::ContextSettingsForm($settings, 'controllers/tab/settings/citations/form/citationsForm.tpl', $wizardMode);
	}

	/**
	 * Fetch the form
	 * @param $request Request
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);

		// Add extra java script required for ajax components
		// FIXME: Must be removed after OMP->OJS backporting
		$templateMgr->addJavaScript('lib/pkp/js/functions/citation.js');
		$templateMgr->addJavaScript('lib/pkp/js/lib/jquery/plugins/validate/jquery.validate.min.js');
		$templateMgr->addJavaScript('lib/pkp/js/functions/jqueryValidatorI18n.js');

		//
		// Citation editor filter configuration
		//

		// 1) Add the filter grid URLs
		$dispatcher = $request->getDispatcher();
		$parserFilterGridUrl = $dispatcher->url($request, ROUTE_COMPONENT, null, 'grid.filter.ParserFilterGridHandler', 'fetchGrid');
		$templateMgr->assign('parserFilterGridUrl', $parserFilterGridUrl);
		$lookupFilterGridUrl = $dispatcher->url($request, ROUTE_COMPONENT, null, 'grid.filter.LookupFilterGridHandler', 'fetchGrid');
		$templateMgr->assign('lookupFilterGridUrl', $lookupFilterGridUrl);

		// 2) Create a list of all available citation output filters.
		$router = $request->getRouter();
		$journal = $router->getContext($request);
		$filterDao = DAORegistry::getDAO('FilterDAO'); /* @var $filterDao FilterDAO */
		$metaCitationOutputFilterObjects = $filterDao->getObjectsByGroup('nlm30-element-citation=>plaintext', $journal->getId());
		foreach($metaCitationOutputFilterObjects as $metaCitationOutputFilterObject) {
			$metaCitationOutputFilters[$metaCitationOutputFilterObject->getId()] = $metaCitationOutputFilterObject->getDisplayName();
		}
		$templateMgr->assign_by_ref('metaCitationOutputFilters', $metaCitationOutputFilters);

		return parent::fetch($request);
	}
}

?>
