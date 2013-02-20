<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menu
 *
 * @author ElyDeLaCruz
 */
class Model_Menu extends Edm_Db_AbstractTable
{
    protected $_name = 'menus';

    public function createMenu(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateMenu($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'menu_id'));
    }

    public function deleteMenu($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'menu_id'));

    }
}