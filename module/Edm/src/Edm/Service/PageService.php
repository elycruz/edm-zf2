<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\Page,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    \stdClass;

/**
 * @todo fix composite data column aware interface and trait to use the 
 * @todo start using the db\table->alias for aliases to avoid conflicts and 
 * maintain readability
 * "tuple" language instead of the array language
 * @author ElyDeLaCruz
 */
class PageService extends AbstractService 
implements \Edm\UserAware,
        \Edm\Db\CompositeDataColumnAware,
        TermTaxonomyServiceAware {
    
    use \Edm\UserAwareTrait,
        \Edm\Db\CompositeDataColumnAwareTrait,
        TermTaxonomyServiceAwareTrait;
    
    /**
     * Page Table
     * @var Zend\Db\Table\TableGateway
     */
    protected $pageTable;
    
    /**
     * Mixed Term Relationship 
     * @var Zend\Db\Table\TableGateway
     */
    protected $mixedTermRelTable;
    
    /**
     * Page Menu Relationship Table
     * @var Zend\Db\Table\TableGateway
     */
    protected $pageMenuRelTable;
    
    /**
     *
     * @var type 
     */
    protected $resultSet;
    
    /**
     *
     * @var type 
     */
    protected $notAllowedForUpdate = array(
        'page_id',
    );

    /**
     * Constructor sets result set, sql, and result set's prototype object
     */
    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new Page());
    }

    /**
     * Creates a page and it's constituants 
     *  (page and page relationship)
     * @param Page $page
     * @return mixed int | boolean | \Exception
     */
    public function createPage(Page $page) {

        // Get current user
        $user = $this->getUser();
        
        // Bail if no user
        if (empty($user)) {
            return false;
        }
        
        // Prepare common values for db
        $cleanData = $this->prepareCommonValuesForDb($page);

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create page
            $this->getPageTable()->insert($cleanData->page);
            $retVal = $page_id = $driver->getLastGeneratedValue();
            
            // Create page mixed term rel for page
            $cleanData->mixedTermRel['object_id'] = $page_id;
            $this->getMixedTermRelTable()->insert($cleanData->mixedTermRel);

//            // If we have a page menu rel
//            if (!empty($cleanData->pageMenuRel) && isset($cleanData->pageMenuRel['menu_id'])) {
//                $cleanData->pageMenuRel['page_id'] = $page_id;
//                $this->getPageMenuRelTable()->insert($cleanData->pageMenuRel);
//            }

            // Commit and return true
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }
    
    /**
     * Updates a page and it's constituants
     *   (mixedTermRel and page mixedTermRel relationship).  
     * @todo There are no safety checks being done in this method
     * @param int $id
     * @param Page $page
     * @return mixed boolean | Exception
     */
    public function updatePage(Page $page) {
        
        // Get current user
        $user = $this->getUser();
        
        // Bail if no user
        if (empty($user)) {
            return false;
        }

        // Get page id
        $id = $page->page_id;

        // Prepare common values for db
        $cleanData = $this->prepareCommonValuesForDb($page);
        
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            
            // Update Mixed Term Rel if necessary
            if (isset($cleanData->mixedTermRel) && is_array($cleanData->mixedTermRel)) {
                $this->getMixedTermRelTable()
                        ->update($cleanData->mixedTermRel, array('page_id' => $id));
            }

            // Update Page
            $this->getPageTable()->update($cleanData->page, array('page_id' => $id));
            
//            // Update Page Menu Rel if necessary
//            if ($this->pageIdInPageMenuRelTable($id) 
//                    && isset($cleanData->pageMenuRel) && is_array($cleanData->pageMenuRel)) {
//                $this->getPageMenuRelTable()
//                        ->update($cleanData->pageMenuRel, array('page_id' => $id));
//            }

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Deletes a page and depends on RDBMS triggers and cascade rules to delete
     * it's related tables (mixedTermRel and page menu rels)
     * @param int $id
     * @return boolean
     */
    public function deletePage($id) {
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create page
            $this->getPageTable()->delete(array('page_id' => $id));

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Gets a page by id
     * @param integer $id
     * @param integer $fetchMode
     * @return mixed array | boolean
     */
    public function getById($id, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array('fetchMode' => $fetchMode,
                    'where' => array('page.page_id' => $id)));
    }

    /**
     * Fetches a page by screen name
     * @param string $alias
     * @param int $fetchMode
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('page.alias' => $alias)));
    }

    /**
     * Returns our pre-prepared select statement
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        $termTaxService = $this->getTermTaxService();
        
        // @todo implement return values only for current role level
        return $select
            
            // Select from Page
            ->from(array('page' => $this->getPageTable()->table))
                
            // Join Mixed Term Rel
            ->join(array('mixedTermRel' => 
                $this->getMixedTermRelTable()->table), 
                    'mixedTermRel.object_id=page.page_id')
//
//            // Join Page Menu Rel
//            ->join(array('pageMenuRel' => 
//                $this->getPageMenuRelTable()->table),
//                    'pageMenuRel.page_id=page.page_id',
//                    array('menu_id'))

            // Join Term Taxonomy
            ->join(array('termTax' => $termTaxService->getTermTaxonomyTable()->table),
                    'termTax.term_taxonomy_id=mixedTermRel.term_taxonomy_id',
                    array('term_alias'))

            // Join Term
            ->join(array('term' => $termTaxService->getTermTable()->table), 
                    'term.alias=termTax.term_alias', array('term_name' => 'name'))

            // Limit from mixed term rel (allow only "page" types) for our page 
            // select statement
            ->where('mixedTermRel.objectType="page"');
    }

    public function getPageTable() {
        if (empty($this->pageTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->pageTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'pages', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->pageTable;
    }

    public function getMixedTermRelTable() {
        if (empty($this->mixedTermRelTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->mixedTermRelTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'mixed_term_relationships', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->mixedTermRelTable;
    }
    
    public function getPageMenuRelTable() {
        if (empty($this->pageMenuRelTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->pageMenuRelTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'page_menu_relationships', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->pageMenuRelTable;
    }
    
    /**
     * Checks if an alias already exists for a page
     * @param string $alias
     * @return boolean 
     */
    public function checkPageAliasExistsInDb($alias) {
        return $this->getMixedTermRelTable()
                ->select(array('alias' => $alias))->valid();
    }
    
    /**
     * Cleans, escapes, serializes (where necessary), and normalizes `Page` data 
     * before entry into database.
     * @param \Edm\Model\Page $page
     * @return \stdClass {(array)page, (array)mixedTermRel, (array)pageMenuRel)}
     */
    protected function prepareCommonValuesForDb(Page $page) {
                
        // Get some help for cleaning data to be submitted to db
        $dbDataHelper = $this->getDbDataHelper();
        
        // Page Term Rel
        $mixedTermRel = $page->getMixedTermRelProto();
        
        // Page Menu Rel
        $pageMenuRel = $page->getMenuPageRelProto();
        
        // Output variable
        $out = new stdClass();
        
        // Ensure required defaults for model values that are not set
        $this->ensureRequiredDefaultsForModel($page);
        
        // Page tuple
        $cleanPage = $dbDataHelper->escapeTuple($page->toArray());
        
        // Mixed Term Rel tuple
        $cleanMixedTermRel = $dbDataHelper->escapeTuple($mixedTermRel->toArray());
        
        // Page Menu Rel tuple
        $cleanPageMenuRel =  $dbDataHelper->escapeTuple($pageMenuRel->toArray());
        
        // Serialize and escape user params if any
        if (is_array($cleanPage['userParams'])) {
            $cleanPage['userParams'] = $this->serializeAndEscapeTuples($cleanPage['userParams']);
        }
        
        // Serialize and escape mvc_params if necessary
        if (is_array($cleanPage['mvc_params'])) {
            $cleanPage['mvc_params'] = $this->serializeAndEscapeTuples($cleanPage['mvc_params']);
        }
        
        // Set values to return
        $out->page = $cleanPage;
        $out->pageMenuRel = $cleanPageMenuRel;
        $out->mixedTermRel = $cleanMixedTermRel;
        
        // Return values
        return $out;
    }
    
    protected function ensureRequiredDefaultsForModel(Page $page) {
        // If empty alias
        if (empty($page->alias)) {
            $page->alias = $this->getDbDataHelper()->getValidAlias($page->title);
        }
                
        // If empty user params
        if (!isset($page->userParams)) {
            $page->userParams = '';
        }
        
        // If empty Html Attribs
        if (empty($page->htmlAttribs)) {
            $page->htmlAttribs = '';
        }
        
        // If empty Mvc Params
        if (empty($page->mvc_params)) {
            $page->mvc_params = '';
        }
        
        // If empty Mvc Params
        if (empty($page->mvc_resetParamsOnRender)) {
            $page->mvc_resetParamsOnRender = 0;
        }
        
        // If empty Mvc Params
        if (empty($page->visible)) {
            $page->visible = 0;
        }
        
        return $page;
    }
    
    /**
     * Checks to see if page_id is in the Page Menu Rel Table
     * @param {int} $id
     * @return {boolean}
     */
    public function pageIdInPageMenuRelTable ($id) {
        return $this->getMixedTermRelTable->select(array('page_id' => $id))->valid();
    }
    
    public function setListOrderForPage (Page $page) {
        if (!is_numeric($page->listOrder) || !is_numeric($page->page_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .' -> '. __FUNCTION__ . '.');
        }
        return $this->getPageTable()->update(
                array('listOrder' => $page->listOrder), 
                array('page_id' => $page->page_id));
    }
    
    public function setTermTaxonomyForPage (Page $page, $taxonomyAlias, $value) {
        
        // If input filter is not valid (data in page is not valid) then
        // throw an exception
        if (!$page->getInputFilter()->isValid()) {
            throw new \Exception('Page object received in ' .
                    __CLASS__ .'->'. __FUNCTION__ . ' is invalid.');
        }
        
        // If taxonomy alias is not valid
        if (!in_array($taxonomyAlias, $page->getValidKeys())) {
            throw new \Exception('"'. $taxonomyAlias . '" is not a valid ' .
                    'field of the page model in "' . 
                    __CLASS__ . '->' . __FUNCTION__ . '"');
        }
        
        // If page id is not set
        if (!is_numeric($page->page_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .'->'. __FUNCTION__ . '\'s $page->page_id param.');
        }

        // Check if taxonomy alias indeed has $value else throw error
        $allowedCheck = $this->getTermTaxService()->getByAlias($value, $taxonomyAlias);
        if (empty($allowedCheck)) {
            throw new \Exception('One of the values passed into "' .
                    __CLASS__ .'->'. __FUNCTION__ . '" are not allowed.');
        }
        
        // Update term taxonomy value and return outcome
        return $this->getPageTable()->update(array($taxonomyAlias => $value), 
                array('page_id' => $page->page_id));
    }

}