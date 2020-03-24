<?php

import('lib.pkp.classes.db.DAO');
import('plugins.generic.forthcoming.classes.ForthcomingArticle');

class ForthcomingArticleDAO extends DAO
{

    function insertObject($forthcomingArticle)
    {
        $this->update(
            'INSERT INTO forthcoming_article (context_id,submission_id) VALUES (?, ?, ?)',
            array(
                (int)$forthcomingArticle->getContextId(),
                (int)$forthcomingArticle->getSubmissionId()
            )
        );
        $forthcomingArticle->setId($this->getInsertId());
        return $forthcomingArticle->getId();
    }

    function getSubmissionIdsByContextId($contextId)
    {
        $result = $this->retrieve(
            'SELECT * FROM forthcoming_article WHERE context_id = ?',
            (int)$contextId
        );

        $awards = array();
        while (!$result->EOF) {
            $row = $result->GetRowAssoc(false);
            $awards[$row['forthcoming_article_id']] = $row['submission_id'];

            $result->MoveNext();
        }
        $result->Close();
        return $awards;
    }

    function newDataObject()
    {
        return new ForthcomingArticle();
    }
}