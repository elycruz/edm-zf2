<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\TermTaxonomy,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql;

/**
 * @author ElyDeLaCruz
 * @todo optimize this service further by resuing the getter functions 
 *      within each other
 */
class TermTaxonomyService extends AbstractService {
    protected $termTable;
    protected $termTaxTable;
    protected $termTable_alias = 'term';
    protected $termTaxTable_alias = 'termTax';
    protected $termTaxProxyTableName = 'term_taxonomies_proxy';
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
        return $this->read(array(
            'fetchMode' => self::FETCH_FIRST_AS_ARRAY,
            'where' => array(
                $this->termTaxTable_alias . '.term_taxonomy_id' => 
                    $term_taxonomy_id )));
    }

    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $taxonomy default 'taxonomy'
     * @param string $alias the taxonomies alias
     * @param array $options default null
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $taxonomy = 'taxonomy', 
            array $options = null) {
        // Default options
        $options1 = array(
            'fetchMode' => self::FETCH_FIRST_AS_ARRAY,
            'where' => array(
                $this->termTaxTable_alias . '.taxonomy' => $taxonomy,
                $this->termTaxTable_alias . '.term_alias' => $alias ));
        
        // If options
        $options = is_array($options) ? 
                array_merge_recursive($options1, $options) : $options1;
        
        // Return results
        return $this->read($options);
    }

    /**
     * Get by Taxonomy
     * @param string $taxonomy
     * @param mixed $options
     * @return array
     */
    public function getByTaxonomy($taxonomy, $options = null) {
        // Default options
        $options1 = array(
            'fetchMode' => self::FETCH_FIRST_AS_ARRAY,
            'where' => array(
                $this->termTaxTable_alias . '.taxonomy' => $taxonomy));
        
        // If options
        $options = is_array($options) ? 
                array_merge_recursive($options1, $options) : $options1;
        
        // Return results
        return $this->read($options);
    }

    /**
     * Sets a term taxonomy's list order value
     * @param int $id
     * @param numeric $listOrder
     * @return mixed boolean | ?
     * @throws \Exception
     */
    public function setListOrderForId($id, $listOrder) {
        if (!is_numeric($listOrder)) {
            throw new \Exception('List order must be numeric value ' .
            'received: ' . $listOrder);
        }
        if (!is_numeric($id)) {
            throw new \Exception('Id must be numeric value ' .
            'received: ' . $id);
        }

        return $this->getTermTaxonomyTable()->updateItem($id, array(
                    'listOrder' => $listOrder
        ));
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
        // @todo implement return values only for current role level?
        return $select
            // Term Taxonomy
            ->from(array('termTax' => $this->getTermTaxonomyTable()->table))

            // Term
            ->join(array('term' => $this->getTermTable()->table), 
                    'term.alias=' . $this->termTaxTable_alias . '.term_alias', 
                    array('term_name' => 'name', 'term_group_alias'))

            // Count table
            ->join(array('termTaxProxy' => $this->termTaxProxyTableName), 
                    'termTaxProxy.term_taxonomy_id=termTax.term_taxonomy_id', 
                    array('childCount', 'assocItemCount'));
    }

    public function createItem(array $data) {
        // Throw error if term or term-taxonomy not set
        if (!isset($data['term']) || !isset($data['term-taxonomy'])) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' requires ' .
            'parameter "$data" to contain a "term" and ' .
            '"term-taxonomy" keys.');
        }

        // Clean data 
        $dbDataHelper = $this->getDbDataHelper();
        $term = $dbDataHelper->escapeTuple($data['term']);
        $termTax = $dbDataHelper->escapeTuple($data['term-taxonomy']);

        // If parent is not greater than 0
        if (empty($termTax['parent_id']) 
                || !is_numeric($termTax['parent_id'])) {
            unset($termTax['parent_id']);
        }
        
        // If empty access group remove it's key
            if (empty($termTax['accessGroup'])) {
            unset($termTax['accessGroup']);
        }
        
        // Normalize description
        $desc = $termTax['description'];
        $termTax['description'] = $desc ? $desc : 'None';

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db insertions
        try {
            // Process Term and rollback if failure
            $termRslt = $this->getTermFromData($term);

            // Set term tax term alias just in case
            $termTax['term_alias'] = $termRslt->alias;

            // Process Term Taxonomy 
            $termTaxRslt = $this->getTermTaxonomyTable()
                    ->createItem($termTax);
            
            // Commit changes
            $conn->commit();

            // Return success message
            return $termTaxRslt;
        } 
        catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();
            return $e;
        }
    }

    public function updateItem($id, $data) {
        // Throw error if term or term-taxonomy not set
        if (!isset($data['term']) || !isset($data['term-taxonomy'])) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' requires ' .
            'parameter "$data" to contain a "term" and ' .
            '"term-taxonomy" keys.');
        }

        // Clean data 
        $dbDataHelper = $this->getDbDataHelper();
        $term = $dbDataHelper->escapeTuple($data['term']);
        $termTax = $dbDataHelper->escapeTuple($data['term-taxonomy']);

        // Normalize description
        $desc = $termTax['description'];
        $termTaxData['description'] = $desc ? $desc : '';

        // Normalize parent id
        $termTaxData['parent_id'] = !empty($termTaxData['parent_id']) ?
                $termTaxData['parent_id'] : 0;

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
            
        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Process Term and rollback if failure
            $termRslt = $this->getTermFromData($term);

            // Set term tax term alias just in case
            $termTax['term_alias'] = $termRslt->alias;

            // Process Term Taxonomy 
            $termTaxRslt = $this->getTermTaxonomyTable()
                    ->updateItem($id, $termTax);

            // Commit changes
            $conn->commit();

            // Return success message
            return $termTaxRslt;
        } catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();
            return $e;
        }
    }

    public function deleteItem($id) {
        // Throw error if term or term-taxonomy not set
        if (!is_numeric((int) $id)) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' expects ' .
            'id to be numeric.');
        }

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Process Term Taxonomy 
            $termTaxRslt = $this->getTermTaxonomyTable()
                    ->deleteItem($id);

            // Commit changes
            $conn->commit();

            // Return success message
            return $termTaxRslt;
        } catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();
            return $e;
        }
    }

    /**
     * Get term from data and create it if it doesn't exists
     * @param mixed [array, object] $termData gets cast as (object) 
     * @return mixed Edm\Model\Term | array
     */
    public function getTermFromData($termData) {
        // Convert from array if necessary
        if (is_array($termData)) {
            $termData = (object) $this->getDbDataHelper()
                            ->escapeTuple($termData);
        }

        // Get term table
        $termTable = $this->getTermTable();

        // Check if term already exists
        $term = $termTable->getByAlias($termData->alias);

        // Create term if empty
        if (empty($term)) {
            $rslt = $termTable->insert((array) $termData);
            if (empty($rslt)) {
                return false;
            }
            $term = $termTable->getByAlias($termData->alias);
        }
        // Update term if data and term are differen
        else if ((!empty($term->name) && $term->name !== $termData->name)) {
            $termTable->updateItem($term->alias, $term->toArray());
            $term->name = $termData->name;
        }
        return $term;
    }

    /**
     * Term Taxonomy Table
     * @return Edm\Db\Table\TermTaxonomyTable
     */
    public function getTermTaxonomyTable() {
        if (empty($this->termTaxTable)) {
            $locator = $this->getServiceLocator();
            $this->termTaxTable = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTaxonomyTable');
            $this->termTaxTable->setServiceLocator($locator);
        }
        return $this->termTaxTable;
    }

    /**
     * Term Table
     * @return Edm\Db\Table\TermTable
     */
    public function getTermTable() {
        if (empty($this->termTable)) {
            $locator = $this->getServiceLocator();
            $this->termTable = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTable');
            $this->termTable->setServiceLocator($locator);
        }
        return $this->termTable;
    }

}