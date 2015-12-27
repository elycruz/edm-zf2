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
    Zend\Db\Sql\Sql,
    Edm\Db\ResultSet\Proto\TermTaxonomyProto,
    Edm\Db\ResultSet\Proto\TermProto;

class TermTaxonomyService extends AbstractCrudService {

    protected $termTable;
    protected $termTaxTable;
    protected $termTaxProxyTable;
    protected $termTaxonomyProto;
    protected $termTable_alias = 'term';
    protected $termTaxTable_alias = 'termTaxonomy';
    protected $termTaxProxyTable_alias = 'termTaxonomyProxy';
    protected $resultSet;

    public function __construct() {
        $this->termTaxonomyProto = new TermTaxonomyProto();
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype($this->termTaxonomyProto);
    }

    /**
     * Gets a term taxonomy by id.
     * @param int $term_taxonomy_id
     * @return TermTaxonomyProto | bool (false)
     */
    public function getTermTaxonomyById($term_taxonomy_id) {
        return $this->read([
            'where' => [$this->termTaxTable_alias . '.term_taxonomy_id' => $term_taxonomy_id]
        ])->current();
    }

    /**
     * Gets a Term Taxonomy by alias and taxonomy
     * @param string $alias the taxonomies alias
     * @param string $taxonomy default 'taxonomy'
     * @param array $options default null
     * @return TermTaxonomyProto | bool (false)
     */
    public function getTermTaxonomyByAlias($alias, $taxonomy = 'taxonomy',
                               array $options = null) {
        // Default options
        $options1 = array(
            'where' => array(
                $this->termTaxTable_alias . '.taxonomy' => $taxonomy,
                $this->termTaxTable_alias . '.term_alias' => $alias ));

        // If options
        $normalizedOptions = is_array($options) ?
            array_merge_recursive($options1, $options) : $options1;

        // Return results
        return $this->read($normalizedOptions)->current();
    }

    /**
     * Get by Taxonomy
     * @param string $taxonomy
     * @param mixed $options
     * @return ResultSet
     */
    public function getTermTaxonomyByTaxonomy($taxonomy, $options = null) {
        // Default options
        $options1 = array(
            'where' => array(
                $this->termTaxTable_alias . '.taxonomy' => $taxonomy));

        // If options
        $normalizedOptions = is_array($options) ?
            array_merge_recursive($options1, $options) : $options1;

        // Return results
        return $this->read($normalizedOptions);
    }

    /**
     * Sets a term taxonomy's list order value
     * @param TermTaxonomyProto $termTaxonomy
     * @return boolean
     * @throws \Exception
     */
    public function setListOrderForTaxonomy(TermTaxonomyProto $termTaxonomy) {
        return $this->getTermTaxonomyTable()->update(
            ['listOrder' => $termTaxonomy->listOrder], 
            ['term_taxonomy_id' => $termTaxonomy->term_taxonomy_id]
        );
    }

    /**
     * Returns our pre-prepared select statement
     * for our term taxonomy model
     * @todo select should include: (parent_name, parent_alias, taxonomy_name)
     * @param Sql $sqlObj
     * @param array $options - Default null.
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect(Sql $sqlObj = null, array $options = null) {
        $sql = isset($sqlObj) ? $sqlObj : $this->getSql();
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
                ['term_name' => 'name', 'name', 'alias', 'term_group_alias'])

            // Count table
            ->join([$termTaxProxyTable->alias => $termTaxProxyTable->table],
                $termTaxProxyTable->alias . '.term_taxonomy_id' .
                '=' . $termTaxTableAlias . '.term_taxonomy_id',
                ['childCount', 'assocItemCount']);
    }

    /**
     * @param TermTaxonomyProto $termTaxonomy
     * @return \Exception | int
     */
    public function createTermTaxonomy(TermTaxonomyProto $termTaxonomy) {
        // Get db data helper for cleaning
        $dbDataHelper = $this->getDbDataHelper();
        
        // Clean incoming data
        $dbDataHelper->escapeTuple($termTaxonomy);

        // Get term data
        $term = $termTaxonomy->getTermProto();
        
        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db insertions
        try {
            // Process Term and rollback if failure
            $this->lazyLoadTerm($term);

            // Process Term Taxonomy
            $this->getTermTaxonomyTable()->insert($termTaxonomy->toArray(
                    TermTaxonomyProto::FOR_OPERATION_DB_INSERT));

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
     * @param TermTaxonomyProto $termTaxonomy
     * @return \Exception|int
     * @throws \Exception
     */
    public function updateTermTaxonomy (TermTaxonomyProto $termTaxonomy) {
        // Clean data
        $dbDataHelper = $this->getDbDataHelper();
                
        // Escape `termTaxonomy`
        $dbDataHelper->escapeTuple($termTaxonomy);
        $term = $termTaxonomy->getTermProto();

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Process Term and rollback if failure
            $this->lazyLoadTerm($term);

            // Process Term Taxonomy
            $this->getTermTaxonomyTable()
                ->update($termTaxonomy->toArray('Insert'),
                        ['term_taxonomy_id' => $termTaxonomy->term_taxonomy_id]);

            // Commit changes
            $conn->commit();

            // Return success message
            return true;
       }
        catch (\Exception $e) {
            // Rollback changes
            $conn->rollback();

            // Return exception
            return $e;
        }
    }

    /**
     * 
     * @param TermTaxonomyProto $termTaxonomy
     * @return \Exception | bool
     * @throws \Exception
     */
    public function deleteTermTaxonomy(TermTaxonomyProto $termTaxonomy) {
        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        // Try db updates
        try {

            // Delete term taxonomy
            $this->getTermTaxonomyTable()
                ->delete(['term_taxonomy_id' => $termTaxonomy->term_taxonomy_id]);
            
            // Return true
            $retVal = true;

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
     * @param TermProto $term
     * @return \Edm\Db\ResultSet\Proto\TermProto
     */
    public function lazyLoadTerm(TermProto $term) {
        // Get term table
        $termTable = $this->getTermTable();

        // Check if term already exists
        $foundTerm = $termTable->select(['alias' => $term->alias])->current();

        // Create term if empty
        if (empty($foundTerm)) {
            $rslt = $termTable->insert($term->toArray(
                    TermTaxonomyProto::FOR_OPERATION_DB_INSERT));
            if (empty($rslt)) {
                return false;
            }
        }
        // Update term if data and term are different
        else if ($foundTerm->name !== $term->name) {
            $termTable->update(['alias' => $foundTerm->alias], $term->toArray());
        }
        return $term;
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

}
