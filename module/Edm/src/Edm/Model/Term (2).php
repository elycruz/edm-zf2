<?php

/**
 * @author ElyDeLaCruz
 */
class Model_Term extends Edm_Db_AbstractTable {

    protected $_name = 'terms';

    /**
     * Create term
     * @param array $data
     * @return integer last inserted id
     */
    public function createTerm(array $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    /**
     * Get term
     * @param <type> $id
     */
    public function getTermById($id) {
        return $this->fetchRow(
            $this->select()->where(
                $this->getWhereClauseFor('alias', $id)));
    }

    /**
     * Gets a term by its alias
     * @param string $alias
     * @return unknown
     */
    public function getTermByAlias($alias)
    {
        return $this->fetchRow(
            $this->select()->where(
                $this->getWhereClauseFor($alias, 'alias')));
    }

    /**
     * Updates a term from the terms table
     * @param <int> $id
     * @param <array> $data
     * @return <boolean>
     */
    public function updateTerm($id, $data)
    {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'alias'));
    }

    /**
     * Deletes a term from the terms table
     * @param Int $id
     * @return Boolean
     */
    public function deleteTerm($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'alias'));
    }

}