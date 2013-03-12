<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Zend\Db\Sql\Sql;

/**
 * @author ElyDeLaCruz
 * @todo find a way to make this class work with roles
 */
class TermTaxonomyService extends AbstractService {

    protected $termModel;
    protected $termTaxModel;
    // @todo implement these and get rid of hardcoded values
    protected $termModel_alias = 'term';
    protected $termTaxModel_alias = 'termTax';

    public function __construct() {
        
    }

    /**
     * Gets a term taxonomy by id
     * @param integer $term_taxonomy_id
     * @return mixed array | boolean
     */
    public function getById($term_taxonomy_id) {
        $sql = new Sql($this->getDb());
        $select = $this->getSelect($sql)->where('termTax.taxonomy="' . $term_taxonomy_id .'"');
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }

    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $taxonomy default 'taxonomy'
     * @param string $alias the taxonomies alias
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $taxonomy = 'taxonomy') {
        $sql = new Sql($this->getDb());
        $select = $this->getSelect($sql)->where('termTax.taxonomy="' . $taxonomy .
                '" AND termTax.term_alias="' . $alias . '"');
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }

    public function getDescendantsByAlias($alias, $taxonomy = 'taxonomy', $options = null) {

        // Expect stdClass as options else convert
        if (!empty($options) && is_array($options)) {
            $options = (object) $options;
        }

        // Get options
        if (empty($options)) {
            $options = new stdClass();
        }

        // Select
        $select = $this->getSelect();

        // Create results array
        $rslts = array();

        // Get db data helper
        $dbDataHelper = $this->getDbDataHelper();

        // Get top tuple
        if (empty($options->topTuple)) {
            $topTuple = (object) $dbDataHelper->reverseEscapeTupleFromDb(
                            $this->getByAlias($alias, $taxonomy));
        } else {
            if (is_array($options->topTuple)) {
                $options->topTuple = (object) $options->topTuple;
            }
            $topTuple = $options->topTuple;
        }

        // Top level tuple's children
        $topChildren = $select->where('termTax.taxonomy=' .
                        $topTuple->term_taxonomy_id)
                ->order('term_name DESC')
                ->query(Zend_Db::FETCH_ASSOC)
                ->fetchAll();

        // If no results bail
        if (!is_array($topChildren)) {
            return null;
        }

        // Escape top children
        $topChildren = $this->escapeTuplesAs_objOrAssoc(
                $topChildren, $fetchMode);

        if (!empty($options->attachChildren)) {
            $topTuple->descendants = $topChildren;
            return $topTuple;
        }

        // Add top tuple to top of results 
        $rslts[] = $topTuple;
        return array_replace($rslts, $topChildren);
    }

    public function getDescendantsByAliasRecursive($alias, $taxonomy = 'taxonomy', $options = null) {

        // Get top tuple
        $topTuples = $this->getDescendantsByAlias($alias, $taxonomy, $options);
        $topTuple = array_pop($topTuples);

        // If top tuple not std class make
        if (!($topTuple instanceOf stdClass)) {
            $topTuple = (object) $topTuple;
        }

        // Get top tuples from descendants if necessary
        if (!empty($topTuple->descendants) && $topTuple->descendants) {
            $topTuples = $topTuple->descendants;
        }

        // New Sub CHildren
        $newTopChildren = array();

        foreach ($topTuples as $topChild) {
            // Get children
            $subChildren = $select
                    ->where('termTax.parent_id=' . $topChild['term_taxonomy_id'])
                    ->order('term_name DESC')
                    ->query(Zend_Db::FETCH_ASSOC)
                    ->fetchAll();

            // If error throw an exception
            if (!is_array($subChildren)) {
                throw new Exception('An error occurred while trying to call ' .
                __FUNCTION__ . ' of the ' . __CLASS__ . ' class.' .
                'Error received:  Failed to retrieve sub children.');
            }

            // Clean sub children
            if (count($subChildren)) {
                $newSubChildren = array();
                foreach ($subChildren as $child) {
                    $child = $this->dbDataHelper
                            ->reverseEscapeTupleFromDb($child);

                    // Set top tuple
                    $options->topTuple = $child;

                    $subSub =
                            $this->_getDescendantsByAliasRecursive(
                            $child['term_alias'], $child['taxonomy'], $options);

                    if ($options->attachChildren) {
                        $child['descendants'] = $subSub;
                    }

                    if ($fetchMode === Zend_Db::FETCH_OBJ) {
                        $child = (object) $child;
                    }

                    $newSubChildren[] = $child;
                }
                $subChildren = $newSubChildren;
            }

            // If attach children
            if ($options->attachChildren) {
                $topChild->descendants = $subChildren;
                $newTopChildren[] = $topChild;
            } else {
                $newTopChildren[] = $topChild;
                $newTopChildren = array_replace($newTopChildren, $subChildren);
            }
        }
    }

    /**
     * Returns terms by taxonomy with optional parent_id constraint
     * @param string $alias
     * @param integer $parent_id default null
     * @param string $sortBy default 'name'
     * @param integer $sort default null
     * @param bool $taxonomizeResult default false
     * @return <type>
     */
    public function getTermTaxonomiesByAlias($alias, $parent_id = null, $sortBy = 'name', $sort = null, $where = null, $taxonomizeResult = false) {
        // This is a Temporary fix
        // @todo fix this functions default values
        $parent_id = !empty($parent_id) ? $parent_id : 0;
        if (!$taxonomizeResult) {
            $select = $this->getSelect()->where('termTax.taxonomy=?', $alias);
            // If parent id:
            if (!empty($parent_id) && is_numeric($parent_id)) {
                $select = $select->where('termTax.parent_id=?', $parent_id);
            }
            // Additional `where` criteria
            if (!empty($where)) {
                $select->where($where);
            }
            // Order our query
            $select->order($sortBy . ' ' . ($sort ? 'DESC' : 'ASC'));

            // Run query
            return $select->query(Zend_Db::FETCH_OBJ)->fetchAll();
        } else {
            $rslts = array();
            // Get all results with parent
            $rslt = $this->getSelect()->where('termTax.taxonomy="' . $alias .
                            '" AND termTax.parent_id = ' . $parent_id)
                    ->order($sortBy . ' ' . ($sort ? 'DESC' : 'ASC'))
                    ->query(Zend_Db::FETCH_OBJ)
                    ->fetchAll();
            foreach ($rslt as $tuple) {
                $rslts[] = $tuple;
                $inner_rslt = $this->getSelect()->where(
                                'termTax.parent_id=' . $tuple->term_taxonomy_id .
                                ' AND termTax.taxonomy="' . $alias . '"')
                        ->order($sortBy . ' ' . ($sort ? 'DESC' : 'ASC'))
                        ->query(Zend_Db::FETCH_OBJ)
                        ->fetchAll();
                $rslts = array_merge($rslts, $inner_rslt);
            }
            return $rslts;
        }
    }

    /**
     * Get all primary taxonomies
     * @return array
     */
    public function getPrimaryTaxonomies() {
        return $this->getTermTaxonomiesByAlias('taxonomy', 0, 'alias');
    }

    /**
     * Returns our pre-prepared select statement 
     * for our term taxonomy model
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect() {
        $sql = $sql ? $sql : new Sql($this->getDb());
        $select = $sql->select();
        // @todo implement return values only for current role level
        return $select
                ->from(array('termTax' => $this->getTermTaxonomyTable()->table))
                ->join(array('term' => $this->getTermTable()->table), 'term.alias=termTax.term_alias', array(
                    'term_name' => 'name',
                    'term_group_alias'));
    }

    /**
     * Returns an escaped set of tuples as a set of objects or arrays
     * @param array $tuples
     * @param int $fetchMode
     * @return array 
     */
    public function escapeTuplesAs_objOrAssoc(array $tuples, $fetchMode) {
        $dbDataHelper = $this->getDbDataHelper();

        // Bail if tuples is an emtpy array
        if (count($tuples) < 1) {
            return $tuples;
        }

        if ($fetchMode === Zend_Db::FETCH_ASSOC) {
            // Clean top children
            $tuples = $dbDataHelper
                    ->reverseEscapeTuplesFromDb($tuples);
        } else {
            $newTuples = array();
            foreach ($tuples as $child) {
                $newTuples[] = (object) $dbDataHelper
                                ->reverseEscapeTupleFromDb($child);
            }
            $tuples = $newTuples;
        }
        return $tuples;
    }

    public function getTermTaxonomyTable() {
        if (empty($this->termTaxModel)) {
            $this->termTaxModel = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTaxonomyTable');
        }
        return $this->termTaxModel;
    }

    public function getTermTable() {
        if (empty($this->termModel)) {
            $this->termModel = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTable');
        }
        return $this->termModel;
    }

}