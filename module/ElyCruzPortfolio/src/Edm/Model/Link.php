<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model_Link
 *
 * @author ElyDeLaCruz
 */
class Model_Link
extends Edm_Db_AbstractTable
{
    protected $_name = 'links';
    

    /**
     * Creates a link entry in our db
     * @param array $data
     * @return integer
     */
    public function createLink(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function readLink($filter = '', $fetchMode = Zend_Db::FETCH_OBJ) {
        $this->select()->where($filter);
    }

    public function updateLink($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'link_id'));
    }

    public function deleteLink($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'link_id'));

    }
}
