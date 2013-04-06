<?php
/**
 * @author ElyDeLaCruz
 */
class Model_PlainHtml extends Edm_Db_AbstractTable
{
    protected $_name = 'plain_htmls';

    public function createPlainHtml(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updatePlainHtml($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'plain_html_id'));
    }

    public function deletePlainHtml($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'plain_html_id'));

    }
}