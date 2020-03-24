<?php

import('lib.pkp.classes.db.DAO');
import('plugins.generic.forthcoming.classes.Forthcoming');

class ForthcomingDAO extends DAO
{

    function getById($contextId)
    {
        $result = $this->retrieve(
            'SELECT * FROM forthcoming WHERE context_id = ?',
            (int)$contextId
        );

        $returner = null;
        if ($result->RecordCount() != 0) {
            $returner = $this->_fromRow($result->GetRowAssoc(false));
        }
        $result->Close();
        return $returner;
    }

    function insertObject($forthcoming)
    {
        $this->update(
            'INSERT INTO forthcoming (context_id) VALUES (?)',
            array(
                (int)$forthcoming->getContextId(),
            )
        );

        $forthcoming->setId($this->getInsertId());
        $this->updateLocaleFields($forthcoming);

        return $forthcoming->getId();
    }

    function updateObject($forthcoming)
    {
        $this->update(
            'UPDATE	forthcoming
			SET	context_id = ?
			WHERE forthcoming_id = ?',
            array(
                (int)$forthcoming->getContextId(),
                (int)$forthcoming->getId()
            )
        );
        $this->updateLocaleFields($forthcoming);
    }


    function _fromRow($row)
    {
        $forthcoming = $this->newDataObject();
        $forthcoming->setId($row['forthcoming_id']);
        $forthcoming->setContextId($row['context_id']);

        $this->getDataObjectSettings('forthcoming_settings', 'forthcoming_id', $row['forthcoming_id'], $forthcoming);

        return $forthcoming;
    }

    function getInsertId()
    {
        return $this->_getInsertId('forthcoming', 'forthcoming_id');
    }

    function newDataObject()
    {
        return new Forthcoming();
    }

    function getLocaleFieldNames()
    {
        return array('title');
    }

    function updateLocaleFields($forthcoming)
    {
        $this->updateDataObjectSettings('forthcoming_settings', $forthcoming, array(
            'forthcoming_id' => $forthcoming->getId()
        ));
    }
}