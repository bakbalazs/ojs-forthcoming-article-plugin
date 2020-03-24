<?php

import('lib.pkp.classes.form.Form');

class ForthcomingSettingsForm extends Form
{
    var $contextId;

    var $plugin;

    function __construct($forthcomingPlugin, $contextId)
    {
        parent::__construct($forthcomingPlugin->getTemplateResource('settingsForm.tpl'));


        $this->contextId = $contextId;
        $this->plugin = $forthcomingPlugin;

        $this->setData('pluginName', $forthcomingPlugin->getName());

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData()
    {
        $forthcomingDAO = DAORegistry::getDAO('ForthcomingDAO');

        $forthcoming = $forthcomingDAO->getById($this->contextId);

        if ($forthcoming) {
            $this->setData('title', $forthcoming->getTitle(null));
        }
    }

    function readInputData()
    {
        $this->readUserVars(array('title'));
    }

    function fetch($request, $template = null, $display = false)
    {
        return parent::fetch($request);
    }

    function execute(...$functionArgs)
    {
        $forthcomingDAO = DAORegistry::getDAO('ForthcomingDAO');
        $forthcoming = $forthcomingDAO->getById($this->contextId);

        if ($forthcoming) {
            $forthcoming->setTitle($this->getData('title'), null);
            $forthcomingDAO->updateObject($forthcoming);
        } else {
            $forthcoming = $forthcomingDAO->newDataObject();
            $forthcoming->setContextId($this->contextId);
            $forthcoming->setTitle($this->getData('title'), null);
            $forthcomingDAO->insertObject($forthcoming);
        }
        parent::execute(...$functionArgs);
    }
}
