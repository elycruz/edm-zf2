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
    \DateTime,
    Zend\Debug\Debug;

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
    
    protected $resultSet;
    protected $notAllowedForUpdate = array(
        'page_id',
    );

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
        
        // Get some help for cleaning data to be submitted to db
        $dbDataHelper = $this->getDbDataHelper();
        
        // Page Term Rel
        $mixedTermRel = $page->getMixedTermRelProto();
        
        // Created Date
        $today = new DateTime();
        $page->createdDate = $today->getTimestamp();
        
        // Created by
        $page->createdById = $user->user_id;

        // If empty alias
        if (empty($page->alias)) {
            $page->alias = $dbDataHelper->getValidAlias($page->title);
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
        if (empty($page->mvc_resetParamsOnRender)) {
            $page->mvc_resetParamsOnRender = 0;
        }
        
        // If empty Mvc Params
        if (empty($page->visible)) {
            $page->visible = 0;
        }
        
        // Escape tuples 
        $cleanPage = $dbDataHelper->escapeTuple($page->toArray());
        $cleanMixedTermRel = $dbDataHelper->escapeTuple($mixedTermRel->toArray());
        if (is_array($cleanPage['userParams'])) {
            $cleanPage['userParams'] = $this->serializeAndEscapeTuples($cleanPage['userParams']);
        }

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create page
            $this->getPageTable()->insert($cleanPage);
            $retVal = $page_id = $driver->getLastGeneratedValue();
            
            // Create page mixed term rel for page
            $cleanMixedTermRel['object_id'] = $page_id;
            $this->getMixedTermRelTable()->insert($cleanMixedTermRel);

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
        $id = $page->page_id;
        // Get Db Data Helper
        $dbDataHelper = $this->getDbDataHelper();
        
        // If empty alias
        if (empty($page->alias)) {
            $page->alias = $dbDataHelper->getValidAlias($page->title);
        }
        
        // Escape tuples 
        $pageData = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($page->toArray()));
        $mixedTermRelData = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($page->getMixedTermRelProto()->toArray()));
        
        // If is array user params serialize it to string
        if (is_array($pageData['userParams'])) {
            $pageData['userParams'] = $this->serializeAndEscapeTuples($pageData['userParams']);
        }
        
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {

            // Update mixedTermRel
            if (is_array($mixedTermRelData) && count($mixedTermRelData) > 0) {
                $this->getMixedTermRelTable()
                        ->update($mixedTermRelData, array('page_id' => $id));
            }

            // Update page
            $this->getPageTable()->update($pageData, array('page_id' => $id));

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
     * it's related tables (mixedTermRel and page mixedTermRel rels)
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
                ->from(array('page' => $this->getPageTable()->table))
                ->join(array('mixedTermRel' => 
                    $this->getMixedTermRelTable()->table), 
                        'mixedTermRel.object_id=page.page_id')
        
            // Term Taxonomy
            ->join(array('termTax' => $termTaxService->getTermTaxonomyTable()->table),
                    'termTax.term_taxonomy_id=mixedTermRel.term_taxonomy_id',
                    array('term_alias'))
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

    /**
     * Checks if an alias already exists for a page
     * @param string $alias
     * @return boolean 
     */
    public function checkPageAliasExistsInDb($alias) {
        $rslt = $this->getMixedTermRelTable()->select(
                        array('alias' => $alias))->current();
        if (empty($rslt)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Remove any empty keys and ones in the not ok for update list
     * @param array $data
     * @return array
     */
    public function ensureOkForUpdate(array $data) {
        foreach ($this->notAllowedForUpdate as $key) {
            if (array_key_exists($key, $data) ||
                    (array_key_exists($key, $data) && !isset($data[$key]))) {
                unset($data[$key]);
            }
        }
        return $data;
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
        return $this->getPageTable()->update(
                array($taxonomyAlias => $value), 
                array('page_id' => $page->page_id));
    }

}