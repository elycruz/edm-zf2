<?php

/**
 * @author ElyDeLaCruz
 * @todo find a way to make this class work with roles
 */
class Edm_Service_Internal_TermTaxonomyService extends
Edm_Service_Internal_AbstractCrudService 
{
    protected $termModel;
    protected $termTaxonomy;
    
    // @todo implement these and get rid of hardcoded values
    protected $termModel_alias = 'term';
    protected $termTaxModel_alias = 'termTax';
    
    // @todo eliminate these and call model->getName() instead?
    // @todo check performance hits on this
    protected $_term_modelName = 'terms';
    protected $_termTaxonomy_modelName = 'term_taxonomies';

    public function __construct() {
        $this->termModel = Edm_Db_Table_ModelBroker::getModel('term');
        $this->termTaxModel = Edm_Db_Table_ModelBroker::getModel('term-taxonomies');
//        $this->_termTaxonomy_modelName = $this->termTaxModel->getName();
//        $this->_term_modelName = $this->termModel->getName();
    }

    /**
     * Gets a term taxonomy by id
     * @param integer $term_taxonomy_id
     * @param integer $fetchMode Zend_Zend_Db::FETCH_*
     * @return type 
     */
    public function getById($term_taxonomy_id, $fetchMode = Zend_Db::FETCH_ASSOC) {
        return $this->getSelect()->where('termTax.term_taxonomy_id=?', $term_taxonomy_id)
            ->query($fetchMode)->fetch();
    }
    
    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $taxonomy default 'taxonomy'
     * @param string $alias the taxonomies alias
     * @param int $fetchMode Zend_Zend_Db::FETCH_* 
     * @return Zend_Db_Table_Row
     */
    public function getByAlias($alias, $taxonomy = 'taxonomy', $fetchMode = Zend_Db::FETCH_ASSOC) {
        return $this->getSelect()
                        ->where('termTax.taxonomy="' . $taxonomy .
                                '" AND termTax.term_alias="' . $alias . '"')
                        ->query($fetchMode)->fetch();
    }
    
    public function getDescendantsByAlias($alias, $taxonomy = 'taxonomy', 
            $options = null, $fetchMode = Zend_Db::FETCH_ASSOC) {
        
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
        }
        else {
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
        if (! is_array($topChildren)) {
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
    
    public function getDescendantsByAliasRecursive($alias, 
            $taxonomy = 'taxonomy', $options = null) {
            
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
                    ->where('termTax.parent_id='. $topChild['term_taxonomy_id'])
                    ->order('term_name DESC')
                    ->query(Zend_Db::FETCH_ASSOC)
                    ->fetchAll();

            // If error throw an exception
            if (!is_array($subChildren)) {
                throw new Exception('An error occurred while trying to call '. 
                        __FUNCTION__ . ' of the '. __CLASS__ .' class.' .
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
                                $child['term_alias'], 
                                $child['taxonomy'], $options);

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
            }
            else {
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
     * @return Zend_Db_Select
     */
    public function getSelect() {
        // @todo implement return values only for current role level
        return $this->getDb()->select()
                        ->from(array('termTax' => $this->_termTaxonomy_modelName))
                        ->join(array('term' => $this->_term_modelName), 
                                'term.alias=termTax.term_alias', array(
                            'term_name' => 'name',
                            'term_group_alias'));
    }
    
    public function setTermModel( Term $model) {
        $this->termModel = $model;
    }

    public function setTermTaxModel( TermTaxonomy $model) {
        $this->termTaxModel = $model;
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
        }
        else {
            $newTuples = array();
            foreach ($tuples as $child) {
                $newTuples[] = (object) $dbDataHelper
                    ->reverseEscapeTupleFromDb($child);
            }
            $tuples = $newTuples;
        }
        return $tuples;
    }
}