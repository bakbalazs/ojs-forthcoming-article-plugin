<?php
/**
 * @file ForthcomingHandler.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.forthcoming
 * @class ForthcomingHandler
 * Find forthcoming content and display it when requested.
 */

import('classes.handler.Handler');

class ForthcomingHandler extends Handler
{
    /** @var ForthcomingPlugin The forthcoming plugin */
    static $plugin;

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Provide the forthcoming plugin to the handler.
     * @param $plugin ForthcomingPlugin
     */
    static function setPlugin($plugin)
    {
        self::$plugin = $plugin;
    }

    /**
     * Handle index request (redirect to "view")
     * @param $args array Arguments array.
     * @param $request PKPRequest Request object.
     */
    function index($args, $request)
    {
        $request->redirect(null, null, 'view', $request->getRequestedOp());
    }

    /**
     * Handle view page request (redirect to "view")
     * @param $args array Arguments array.
     * @param $request PKPRequest Request object.
     */
    function view($args, $request)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
        $context = $request->getContext();
        $contextId = $context->getId();
        $templateMgr = TemplateManager::getManager($request);
        $this->setupTemplate($request);

        $forthcomingArticleDAO = DAORegistry::getDAO('ForthcomingArticleDAO');

        $submissionIds = $forthcomingArticleDAO->getSubmissionIdsByContextId($contextId);

        $submissions = array();
        foreach ($submissionIds as $submissionId) {
            $submissionService = Services::get('submission');
            $submission = $submissionService->get($submissionId);

            if ($submission &&  $submission->getStatusKey() == "submission.status.published") $submissions[] = $submission;
        }

		$forthcomingDAO = DAORegistry::getDAO('ForthcomingDAO');

		$forthcoming = $forthcomingDAO->getById($contextId);

        if (is_null($forthcoming )) {
            $title = __("plugins.generic.forthcoming.defaultPageTitle");
        }else {
            $title = $forthcoming->getLocalizedTitle();

        }

		$templateMgr->assign('title', $title);

        $templateMgr->assign('forthcoming', $submissions);
        $templateMgr->display(self::$plugin->getTemplateResource('forthcoming.tpl'));
    }

}