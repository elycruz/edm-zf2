<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/16/2015
 * Time: 8:20 AM
 */

namespace Edm\Service;

use Zend\Db\ResultSet\ResultSet,
    Edm\Db\ResultSet\Proto\TermTaxonomyProto;

class TermTaxonomyService extends AbstractService {

    protected $termTable;
    protected $termTaxTable;
    protected $termTaxProxyTable;
    protected $termTable_alias = 'term';
    protected $termTaxTable_alias = 'termTaxonomy';
    protected $termTaxProxyTable_alias = 'termTaxonomyProxy';
    protected $resultSet;

    public function __construct($serviceLocator = null) {
        if ($serviceLocator != null) {
            $this->setServiceLocator($serviceLocator);
        }
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new TermTaxonomyProto());
    }

    /**
     * Gets a term taxonomy by id
     * @param int $term_taxonomy_id
     * @return TermTaxonomyProto | bool (false)
     */
    public function getById($term_taxonomy_id) {
        return $this->read([
            'where' => [$this->termTaxTable_alias . '.term_taxonomy_id' => $term_taxonomy_id]
        ])->current();
    }

    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $taxonomy default 'taxonomy'
     * @param string $alias the taxonomies alias
     * @param array $options default null
     * @return TermTaxonomyProto | bool (false)
     */
    public function getByAlias($alias, $taxonomy = 'taxonomy',
                               array $options = null) {
        // Default options
        $options1 = array(
            'where' => array(
                $this->termTaxTable_alias . '.taxonomy' => $taxonomy,
                $this->termTaxTable_alias . '.term_alias' => $alias ));

        // If options
        $options = is_array($options) ?
            array_merge_recursive($options1, $options) : $options1;

        // Return results
        return $this->read($options)->current();
    }

    /**
     * Get by Taxonomy
     * @param string $taxonomy
     * @param mixed $options
     * @return ResultSet
     */
    public function getByTaxonomy($taxonomy, $options = null) {
        // Default options
        $options1 = array(
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
     * @param int $listOrder
     * @return mixed boolean | ?
     * @throws \Exception
     */
    public function setListOrderForId(\int $id, \int $listOrder) {
        return $this->getTermTaxonomyTable()->update(
            ['listOrder' => $listOrder], ['term_taxonomy_id' => $id]
        );
    }

    /**
     * Returns our pre-prepared select statement
     * for our term taxonomy model
     * @todo select should include: (parent_name, parent_alias, taxonomy_name)
     * @param \Zend\Db\Sql\Sql $sql
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = isset($sql) ? $sql : $this->getSql();
        $select = $sql->select();
        $termTaxTable = $this->getTermTaxonomyTable();
        $termTaxProxyTable = $this->getTermTaxonomyProxyTable();
        $termTable = $this->getTermTable();
        return $select
            // Term Taxonomy
            ->from([$termTaxTable->alias => $termTaxTable->table])

            // Term
            ->join([$termTable->alias => $termTable->table],
                $termTable->alias . '.alias=' . $termTaxTable->alias . '.term_alias',
                ['term_name' => 'name', 'term_group_alias'])

            // Count table
            ->join([$termTaxProxyTable->alias => $termTaxProxyTable->table],
                $termTaxProxyTable->alias . '.term_taxonomy_id' .
                '=' . $termTaxTable->alias . '.term_taxonomy_id',
                ['childCount', 'assocItemCount']);
    }

    public function create(TermTaxonomyProto $data) {
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

    public function update($id, $data) {
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

    public function delete($id) {
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
        $term = $termTable->select(['alias' => $termData->alias])->current();

        // Create term if empty
        if (empty($term)) {
            $rslt = $termTable->insert((array) $termData);
            if (empty($rslt)) {
                return false;
            }
            $term = $termTable->getOneWhere(['alias' => $termData->alias]);
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
     * @return \Edm\Db\TableGateway\TermTaxonomyTable
     */
    public function getTermTaxonomyTable() {
        if (empty($this->termTaxTable)) {
            $locator = $this->getServiceLocator();
            $this->termTaxTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\TermTaxonomyTable');
            $this->termTaxTable->setServiceLocator($locator);
        }
        return $this->termTaxTable;
    }

    /**
     * Term Taxonomy Table
     * @return \Edm\Db\TableGateway\TermTaxonomyProxyTable
     */
    public function getTermTaxonomyProxyTable() {
        if (empty($this->termTaxProxyTable)) {
            $locator = $this->getServiceLocator();
            $this->termTaxProxyTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\TermTaxonomyProxyTable');
            $this->termTaxProxyTable->setServiceLocator($locator);
        }
        return $this->termTaxProxyTable;
    }

    /**
     * Term Table
     * @return \Edm\Db\TableGateway\TermTable
     */
    public function getTermTable() {
        if (empty($this->termTable)) {
            $locator = $this->getServiceLocator();
            $this->termTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\TermTable');
            $this->termTable->setServiceLocator($locator);
        }
        return $this->termTable;
    }

}