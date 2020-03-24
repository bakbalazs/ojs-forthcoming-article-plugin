<?php

/**
 * @file ForthcomingPlugin.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.forthcoming
 * @class ForthcomingPlugin
 * Static pages plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');
define('FORTHCOMING_NMI_TYPE', 'NMI_TYPE_FORTHCOMING');

class ForthcomingPlugin extends GenericPlugin
{
    /**
     * Get the plugin's display (human-readable) name.
     * @return string
     */
    function getDisplayName()
    {
        return __('plugins.generic.forthcoming.displayName');
    }

    /**
     * Get the plugin's display (human-readable) description.
     * @return string
     */
    function getDescription()
    {
        return __('plugins.generic.forthcoming.description');
    }


    /**
     * Register the plugin, attaching to hooks as necessary.
     * @param $category string
     * @param $path string
     * @return boolean
     */
    function register($category, $path, $mainContextId = NULL)
    {
        if (parent::register($category, $path)) {
            if ($this->getEnabled()) {
                import('plugins.generic.forthcoming.classes.ForthcomingDAO');
                $forthcomingDAO = new ForthcomingDAO();
                DAORegistry::registerDAO('ForthcomingDAO', $forthcomingDAO);
                import('plugins.generic.forthcoming.classes.ForthcomingArticleDAO');
                $forthcomingArticleDAO = new ForthcomingArticleDAO();
                DAORegistry::registerDAO('ForthcomingArticleDAO', $forthcomingArticleDAO);
                // Intercept the LoadHandler hook to present forthcoming toc when requested.
                HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));
                HookRegistry::register('NavigationMenus::itemTypes', array($this, 'addMenuItemTypes'));
                HookRegistry::register('NavigationMenus::displaySettings', array($this, 'setMenuItemDisplayDetails'));
                HookRegistry::register('SitemapHandler::createJournalSitemap', array($this, 'addSiteMapURLs'));

                HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', array($this, 'metadataFieldEdit'));
                HookRegistry::register('Template::Workflow::Publication', array($this, 'addToPublicationForms'));
            }
            return true;
        }
        return false;
    }

    function addToPublicationForms($hookName, $params) {
        $smarty =& $params[1];
        $output =& $params[2];
        $submission = $smarty->get_template_vars('submission');
        $smarty->assign([
            'submissionId' => $submission->getId(),
        ]);

        $output .= sprintf(
            '<tab id="forthcoming" label="%s">%s</tab>',
            __('Forthcoming'),
            $smarty->fetch($this->getTemplateResource('forthcomingForm.tpl'))
        );

        return false;
    }

    function metadataFieldEdit($hookName, $params) {
        $smarty =& $params[1];
        $output =& $params[2];
        $output .= $smarty->fetch($this->getTemplateResource('forthcomingForm.tpl'));
        return false;
    }

    /**
     * @param $hookName string The name of the invoked hook
     * @param $args array Hook parameters
     * @return boolean Hook handling status
     */
    function callbackHandleContent($hookName, $args)
    {
        $request = $this->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $page =& $args[0];
        if ($page == "forthcoming") {
            define('HANDLER_CLASS', 'ForthcomingHandler');
            $this->import('ForthcomingHandler');
            ForthcomingHandler::setPlugin($this);
            return true;
        }
        return false;
    }

    public function addMenuItemTypes($hookName, $args)
    {
        $types =& $args[0];

        $types
        [FORTHCOMING_NMI_TYPE] = array(
            'title' => __('plugins.generic.forthcoming.navMenuItem'),
            'description' => __('plugins.generic.forthcoming.navMenuItem.description'),

        );
    }

    public function setMenuItemDisplayDetails($hookName, $args)
    {
        $navigationMenuItem =& $args[0];
        $typePrefixLength = strlen(FORTHCOMING_NMI_TYPE);
        if (substr($navigationMenuItem->getType(), 0, $typePrefixLength) === FORTHCOMING_NMI_TYPE) {
            $request = Application::getRequest();
            $dispatcher = $request->getDispatcher();
            $navigationMenuItem->setUrl($dispatcher->url(
                $request,
                ROUTE_PAGE,
                null,
                'forthcoming',
                'view'
            ));
        }
    }

    function addSiteMapURLs($hookName, $args)
    {
        $doc = $args[0];
        $rootNode = $doc->documentElement;
        $request = Application::getRequest();
        $context = $request->getContext();
        if ($context) {
            $url = $doc->createElement('url');
            $url->appendChild($doc->createElement('loc', htmlspecialchars($request->url($context->getPath(), 'forthcoming', 'view'), ENT_COMPAT, 'UTF-8')));
            $rootNode->appendChild($url);
        }
        return false;
    }

    public function getActions($request, $verb)
    {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled() ? array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ) : array(),
            parent::getActions($request, $verb)
        );
    }

    function manage($args, $request)
    {
        $this->import('settings/ForthcomingSettingsForm');

        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $settingsForm = new ForthcomingSettingsForm($this, $context->getId());
                $settingsForm->initData();
                return new JSONMessage(true, $settingsForm->fetch($request));
            case 'save':
                $context = $request->getContext();
                $settingsForm = new ForthcomingSettingsForm($this, $context->getId());
                $settingsForm->readInputData();
                if ($settingsForm->validate()) {
                    // Save the results
                    $settingsForm->execute();
                    $notificationManager = new NotificationManager();
                    $notificationManager->createTrivialNotification(
                        $request->getUser()->getId(),
                        NOTIFICATION_TYPE_SUCCESS,
                        array('contents' => __('plugins.forthcoming.settings.saved'))
                    );
                    return DAO::getDataChangedEvent();
                } else {
                    // Present any errors
                    return new JSONMessage(true, $settingsForm->fetch($request));
                }
        }
        return parent::manage($args, $request);
    }

    function getInstallSchemaFile()
    {
        return $this->getPluginPath() . '/schema.xml';
    }

    function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    function getInstallSitePluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

}