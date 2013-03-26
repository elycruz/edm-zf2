<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model_Module
 *
 * @author ElyDeLaCruz
 */
class Model_ViewModule
extends Edm_Db_AbstractTable
{
    protected $_name = 'view_modules';

    public function createViewModule(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateViewModule($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'view_module_id'));
    }

    public function deleteViewModule($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'view_module_id'));
    }
}
