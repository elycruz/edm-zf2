<?php
/**
 * @author ElyDeLaCruz
 */
class Model_TermTaxonomies
    extends Edm_Db_AbstractTable
{
    /**
     * This models db table name
     * @var string
     */
    protected $_name = 'term_taxonomies';

    /**
     * Sets the list order for a term taxonomy
     * @param integer $id
     * @param integer $listOrder
     * @return Boolean
     */
    public function setListOrder($id, $listOrder) {
        return $this->update(array('listOrder' => $listOrder),
                $this->getWhereClauseFor($id, 'term_taxonomy_id'));
    }
    
}