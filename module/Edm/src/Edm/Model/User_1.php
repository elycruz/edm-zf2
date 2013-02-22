<?php
/**
 * Our user model for application
 *
 * @author ElyDeLaCruz
 */
class Model_User extends Edm_Db_AbstractTable
{
    protected $_name = 'users';

    public function createUser(array $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateUser($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'user_id'));
    }

    public function deleteUser($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'user_id'));
    }

    /**
     * Updates the users lastLogin column
     * @param integer $id
     * @return boolean
     */
    public function updateLastLoginForId($id)
    {
        $now = Zend_Date::now()->getTimestamp();
        return $this->update(array('lastLogin' => $now), 
                $this->getWhereClauseFor($id, 'user_id'));
    }

}


