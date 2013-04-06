<?php
/**
 * Our user model for application
 *
 * @author ElyDeLaCruz
 */
namespace Edm\Table;

use Edm\Model\AbstractTable,
    Edm\Model\User;

class UserTable extends AbstractTable
{
    public function create(User $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function update($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'user_id'));
    }

    public function delete($id) {
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


