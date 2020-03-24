<?php

class Forthcoming extends DataObject
{
    function getContextId()
    {
        return $this->getData('contextId');
    }

    function setContextId($contextId)
    {
        return $this->setData('contextId', $contextId);
    }

    function setTitle($title, $locale)
    {
        return $this->setData('title', $title, $locale);
    }

    function getTitle($locale)
    {
        return $this->getData('title', $locale);
    }

    function getLocalizedTitle()
    {
        return $this->getLocalizedData('title');
    }
}