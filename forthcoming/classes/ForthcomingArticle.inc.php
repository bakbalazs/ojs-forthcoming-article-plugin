<?php

class ForthcomingArticle extends  DataObject
{
    function getContextId()
    {
        return $this->getData('contextId');
    }

    function setContextId($contextId)
    {
        return $this->setData('contextId', $contextId);
    }

    function getSubmissionId()
    {
        return $this->getData('submissionId');
    }

    function setSubmissionId($submissionId)
    {
        return $this->setData('submissionId', $submissionId);
    }
}