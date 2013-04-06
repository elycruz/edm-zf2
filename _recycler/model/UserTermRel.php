<?php
/**
 * Our user model for application
 *
 * @author ElyDeLaCruz
 */
class Model_UserTermRel extends Edm_Db_AbstractTable
{
    protected $_name = 'user_term_relationships';

    public function createUserTermRel(array $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateUserTermRel($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'email'));
    }

    public function deleteUserTermRel($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'email'));
    }
}


