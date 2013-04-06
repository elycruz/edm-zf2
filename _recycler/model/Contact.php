<?php
/**
 * Our user model for application
 *
 * @author ElyDeLaCruz
 */
class Model_Contact extends Edm_Db_AbstractTable
{
    protected $_name = 'contacts';

    public function createContact(array $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateContact($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'contact_id'));
    }

    public function deleteContact($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'contact_id'));
    }
    
}


