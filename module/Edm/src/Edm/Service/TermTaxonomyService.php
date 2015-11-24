<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/16/2015
 * Time: 8:20 AM
 * @todo Look further into ZF's implementation of the ResultSet object and it's uses.
 */

namespace Edm\Service;

use Zend\Db\ResultSet\ResultSet,
    Edm\Db\ResultSet\Proto\TermTaxonomyProto;

class TermTaxonomyService extends AbstractCrudService {

    protected $termTable;
    protected $termTaxTable;
    protected $termTaxProxyTable;
    protected $termTaxonomyProto;
    protected $termTable_alias = 'term';
    protected $termTaxTable_alias = 'termTaxonomy';
    protected $termTaxProxyTable_alias = 'termTaxonomyProxy';
    protected $resultSet;

    public function __construct($serviceLocator = null) {
        if ($serviceLocator != null) {
            $this->setServiceLocator($serviceLocator);
        }
        $this->termTaxonomyProto = new TermTaxonomyProto();
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype($this->termTaxonomyProto);
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
    public function setListOrderById($id, $listOrder) {
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
        $termTaxTableAlias = $termTaxTable->alias;
        return $select
            // Term Taxonomy
            ->from([$termTaxTableAlias => $termTaxTable->table])

            // Term
            ->join([$termTable->alias => $termTable->table],
                $termTable->alias . '.alias=' . $termTaxTableAlias . '.term_alias',
                ['term_name' => 'name', 'term_group_alias'])

            // Count table
            ->join([$termTaxProxyTable->alias => $termTaxProxyTable->table],
                $termTaxProxyTable->alias . '.term_taxonomy_id' .
                '=' . $termTaxTableAlias . '.term_taxonomy_id',
                ['childCount', 'assocItemCount']);
    }

    /**
     * @param array $data
     * @return \Exception|int
     * @throws \Edm\Db\Exception
     * @throws \Exception
     */
    public function create($data) {
        // Throw error if term or term-taxonomy not set
        if (!isset($data['term']) || !isset($data['term-taxonomy'])) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' requires ' .
                'parameter "$data" to contain a "term" and a ' .
                '"term-taxonomy" key.');
        }

        // Set return value
        $retVal = null;

        // Get db data helper for cleaning
        $dbDataHelper = $this->getDbDataHelper();

        // Clean incoming data
        $data = $dbDataHelper->escapeTuple($data);

        // Get term data
        $term = $data['term'];

        // Get term taxonomy data
        $termTax = $data['term-taxonomy'];

        // If parent is not greater than 0 then don't allow it to get flushed in our `toArray` call
        if (isset($termTax['parent_id']) && !is_numeric($termTax['parent_id'])) {
            unset($termTax['parent_id']);
        }

        // If empty access group remove it's key
        if (empty($termTax['accessGroup'])) {
            unset($termTax['accessGroup']);
        }

        // Normalize description
        $desc = $termTax['description'];
        $termTax['description'] = isset($desc) ? $desc : '';

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db insertions
        try {
            // Process Term and rollback if failure
            $termRslt = $this->lazyLoadTerm($term);

            // Set term tax 'term alias' just in case they are different
            $termTax['term_alias'] = $termRslt->alias;

            // Process Term Taxonomy
            $this->getTermTaxonomyTable()->insert($termTax);

            $retVal = $conn->getLastGeneratedValue();

            // Commit changes
            $conn->commit();
        }
        catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();

            // Return caught exception
            $retVal = $e;
        }

        // Return result
        return $retVal;
    }

    /**
     * @param int $id
     * @param array $data
     * @return \Exception|int
     * @throws \Exception
     */
    public function update($id, $data) {
        // Throw error if term or term-taxonomy not set
        if (!isset($data['term']) || !isset($data['term-taxonomy'])) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' requires ' .
                'parameter "$data" to contain a "term" and a ' .
                '"term-taxonomy" key.');
        }

        // Clean data
        $dbDataHelper = $this->getDbDataHelper();
        $data = $dbDataHelper->escapeTuple($data);
        $termTax = $data['term-taxonomy'];
        $term = $data['term'];

        // Set term's alias if it is not set
        // assumes termTax has term_alias field.
        if (!isset($term['alias'])) {
            $term['alias'] = $termTax['term_alias'];
        }

        // Normalize description
        $desc = $termTax['description'];
        $termTax['description'] = $desc ? $desc : '';

        // Normalize parent id
        $termTax['parent_id'] = !empty($termTax['parent_id']) ?
            $termTax['parent_id'] : 0;

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Process Term and rollback if failure
            $termRslt = $this->lazyLoadTerm($term);

            // Set term tax term alias just in case
            $termTax['term_alias'] = $termRslt->alias;

            // Process Term Taxonomy
            $this->getTermTaxonomyTable()
                ->update(['term_taxonomy_id' => $id], $termTax);

            $termTaxRslt = $conn->getLastGeneratedValue();

            // Commit changes
            //$conn->commit();

            // Return success message
            return $termTaxRslt;
       }
        catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();

            // Return exception
            return $e;
        }
    }

    public function delete($id) {
        // Throw error if term or term-taxonomy not set
        if (!is_numeric($id)) {
            throw new \Exception(__CLASS__ . '.' . __FUNCTION__ . ' expects id to be numeric.');
        }

        // Set return value
        $retVal = null;

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Find taxonomy to delete
        $foundTermTaxonomy = $this->getById($id);

        // If term taxonomy to delete is not found throw error for now
        // @todo decide what to do here except throwing an error
        if (empty($foundTermTaxonomy)) {
            throw new \Exception('Invalid id passed in for term taxonomy delete.');
        }

        // Check for other usages of term_alias
        $otherRslts = $this->read([
            'where' => ['term_alias' => $foundTermTaxonomy->term_alias]
        ]);

        // If term of term taxonomy to delete is not being used anywhere else delete it
        $deleteTerm = $otherRslts->count() == 1;

        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Delete term taxonomy
            $this->getTermTaxonomyTable()
                ->delete(['term_taxonomy_id' => $id]);

            // Delete 'term' if necessary
            if ($deleteTerm) {
                $this->getTermTable()
                    ->delete(['alias' => $foundTermTaxonomy->term_alias]);
            }

            // Return last generated/updated value (primary key)
            $retVal = $driver->getLastGeneratedValue();

            // Commit changes
            $conn->commit();
        }
        catch (\Exception $e) {

            // Rollback changes
            $conn->rollback();

            // Set return value
            $retVal = $e;
        }

        return $retVal;
    }

    /**
     * Fetch term proto from db.  If it is not there create it and return.
     * @todo add escaping to this lazy loading functionality.
     * @param array $termData
     * @return \Edm\Db\ResultSet\Proto\TermProto
     */
    public function lazyLoadTerm($termData) {
        // Get term table
        $termTable = $this->getTermTable();

           // Check if term already exists
        $term = $termTable->select(['alias' => $termData['alias']])->current();

        // Create term if empty
        if (empty($term)) {
            $rslt = $termTable->insert($termData);
            if (empty($rslt)) {
                return false;
            }
            $term = $termTable->getOneWhere(['alias' => $termData['alias']]);
        }
        // Update term if data and term are different
        else if ((!empty($termData['name']) && $termData['name'] !== $term->name)) {
            $term->exchangeArray($termData);
            $termTable->update(['alias' => $term->alias], $term->toArray());
        }
        return $term;
    }

    /**
     * Term Taxonomy Table
     * @return \Edm\Db\TableGateway\TermTaxonomyTable
     */
    public function getTermTaxonomyTable() {
        if (empty($this->termTaxTable)) {
            $this->termTaxTable = $this->getServiceLocator()
                ->get('Edm\\Db\\TableGateway\\TermTaxonomyTable');
        }
        return $this->termTaxTable;
    }

    /**
     * Term Taxonomy Table
     * @return \Edm\Db\TableGateway\TermTaxonomyProxyTable
     */
    public function getTermTaxonomyProxyTable() {
        if (empty($this->termTaxProxyTable)) {
            $this->termTaxProxyTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\TermTaxonomyProxyTable');
        }
        return $this->termTaxProxyTable;
    }

    /**
     * Term Table
     * @return \Edm\Db\TableGateway\TermTable
     */
    public function getTermTable() {
        if (empty($this->termTable)) {
            $this->termTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\TermTable');
        }
        return $this->termTable;
    }

    /**
     * Term Taxonomy Proto.
     * @return \Edm\Db\ResultSet\Proto\TermTaxonomyProto
     */
    public function getTermTaxonomyProto () {
        return $this->termTaxonomyProto;
    }

}
