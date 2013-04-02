<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\TermTaxonomy,
    Zend\Db\ResultSet\ResultSet,
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
    protected $resultSet;

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new TermTaxonomy());
    }

    /**
     * Gets a term taxonomy by id
     * @param integer $term_taxonomy_id
     * @return mixed array | boolean
     */
    public function getById($term_taxonomy_id) {
        $sql = $this->sql;
        $select = $this->getSelect($sql)->where($this->termTaxModel_alias .'.taxonomy="' . $term_taxonomy_id . '"');
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }

    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $taxonomy default 'taxonomy'
     * @param string $alias the taxonomies alias
     * @param mixed $options
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $taxonomy = 'taxonomy', $options = null) {
        $sql = $this->getSql();
        $select = $this->getSelect($sql)->where($this->termTaxModel_alias .'.taxonomy="' . $taxonomy .
                '" AND '. $this->termTaxModel_alias .'.term_alias="' . $alias . '"');
        return $this->resultSet->initialize(
                $sql->prepareStatementForSqlObject($select)->execute()
            )->current();
    }
    
    /**
     * Get by Taxonomy
     * @param string $taxonomy
     * @param mixed $options
     * @return array
     */
    public function getByTaxonomy ($taxonomy, $options = null) {
        // Normalize/get options object and seed it with default select params
        $options = $this->seedOptionsForSelect(
                $this->normalizeMethodOptions($options));
        
        // Get results
        $rslt = $this->resultSet->initialize(
            $options->sql->prepareStatementForSqlObject(
                $options->select->where($this->termTaxModel_alias .
                        '.taxonomy="' . $taxonomy . '"')
            )->execute());
        
        return $this->fetchFromResult($rslt, $options->fetchMode);
    }
    
    public function getDescendantsByAlias($alias, $taxonomy = 'taxonomy', $options = null) {
        // Normalize options
        $options = $this->normalizeMethodOptions($options);

        // Sql 
        $sql = new Sql($this->getDb());

        // Select
        $select = $this->getSelect($sql);

        // Create results array
        $rslts = array();

        // Get db data helper
        $dbDataHelper = $this->getDbDataHelper();

        // Top tuple
        $topTuple = $this->getByAlias($alias, $taxonomy);

        // If tuple not found return null;
        if (empty($topTuple)) {
            return null;
        }

        // Get top tuple
        if (empty($options->topTuple)) {
            $topTuple = (object) $dbDataHelper->reverseEscapeTuple($topTuple);
        } else {
            if (is_array($options->topTuple)) {
                $options->topTuple = (object) $options->topTuple;
            }
            $topTuple = $options->topTuple;
        }

        // Top level tuple's children
        $topChildren = $this->resultSet;
        $topChildren->initialize(
                $sql->prepareStatementForSqlObject(
                        $select->where($this->termTaxModel_alias .'.taxonomy="' . $alias . '"')
                                ->order('term_name DESC'))->execute());

        // If no top children
        if ($topChildren->count() == 0) {
            return null;
        }

        // If attach children
        if (!empty($options->attachChildren)) {
            $topTuple->descendants = $dbDataHelper
                    ->reverseEscapeTuples($topChildren->toArray());
            $topTuple = (object) $topTuple;
            return $topTuple;
        }

        // Add top tuple to top of results 
        $rslts[] = (object) $topTuple;
        return array_replace($rslts, $topChildren->toArray());
    }

    public function getDescendantsByAliasRecursive($alias, $taxonomy = 'taxonomy', $options = null) {

        // Normalize method options
        $options = $this->normalizeMethodOptions($options);

        // Get top tuples
        $topTuples = $this->getDescendantsByAlias($alias, $taxonomy, $options);

        // Return null if no descendants
        if (!is_array($topTuples) || count($topTuples) == 0) {
            return null;
        }

        // Get top tuple
        $topTuple = array_pop($topTuples);

        // If top tuple not std class make
        if (!($topTuple instanceOf \stdClass)) {
            $topTuple = (object) $topTuple;
        }

        // Get top tuples from descendants if necessary
        if (!empty($topTuple->descendants)) {
            $topTuples = $topTuple->descendants;
        }

        // New Sub CHildren
        $newTopChildren = array();

        foreach ($topTuples as $topChild) {
            $sql = new Sql($this->getDb());

            // Get children
            $subChildren = $this->resultSet->initialize($sql->prepareStatementForSqlObject(
                            $this->getSelect($sql)
                                    ->where($this->termTaxModel_alias .'.parent_id=' . $topChild['term_taxonomy_id'])
                                    ->order('term_name DESC'))->execute());

            // If error throw an exception
            if ($subChildren->count() == 0) {
                throw new Exception('An error occurred while trying to call ' .
                __FUNCTION__ . ' of the ' . __CLASS__ . ' class.' .
                'Error received:  Failed to retrieve sub children.');
            }

            // Clean sub children and add sub sub children 
            while ($subChildren->valid()) {
                $child = $subChildren->current();
                $child->setData(
                        $this->dbDataHelper
                            ->reverseEscapeTuple($child->toArray()));

                // Set top tuple
                $options->topTuple = $child;
                $subSub =
                        $this->getDescendantsByAliasRecursive(
                        $child->term_alias, $child->taxonomy, $options);

                $child->descendants = $subSub;
                $child->altered = true;
                $subChildren->next();
            }
            
            var_dump($subChildren->toArray());
            exit();
            
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
     * Returns our pre-prepared select statement 
     * for our term taxonomy model
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        // @todo implement return values only for current role level
        return $select
            ->from(array('termTax' => $this->getTermTaxonomyTable()->table))
            ->join(array('term' => $this->getTermTable()->table), 
                    'term.alias='. $this->termTaxModel_alias .'.term_alias', array(
                'term_name' => 'name',
                'term_group_alias'));
        
    }

    /**
     * Term Taxonomy Table
     * @return Edm\Db\Table\TermTaxonomyTable
     */
    public function getTermTaxonomyTable() {
        if (empty($this->termTaxModel)) {
            $this->termTaxModel = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTaxonomyTable');
        }
        return $this->termTaxModel;
    }

    /**
     * Term Table
     * @return Edm\Db\Table\TermTable
     */
    public function getTermTable() {
        if (empty($this->termModel)) {
            $this->termModel = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTable');
        }
        return $this->termModel;
    }

}